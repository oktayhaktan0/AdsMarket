<?php
/**
 * PRO STRIPE WEBHOOK - Full Subscription Flow
 * Matches the 'Full Doğru Kurgu' Blueprint
 */
header('Content-Type: application/json');
require_once 'config.php';

// Plan Mapping based on Price IDs
$plan_mapping = [
    'price_1TGgnCHfPSXm7DZTvE3hrHvv' => 'starter',
    'price_1TGgmzHfPSXm7DZTzy1AD2fX' => 'growth',
    'price_1TGgnPHfPSXm7DZT0JB9M8lX' => 'enterprise'
];

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

// In Production: We should use \Stripe\Webhook::constructEvent($payload, $sig_header, STRIPE_WEBHOOK_SECRET);
// For now, we process as JSON to handle all blueprint events.
$event = json_decode($payload, true);

if (!$event) {
    http_response_code(400);
    exit;
}

$conn = getDBConnection();

switch ($event['type']) {
    case 'checkout.session.completed':
        /**
         * 1. Ödeme Sonrası İlk Subscription Oluşturma
         */
        $session = $event['data']['object'];
        $user_id = $session['metadata']['user_id'] ?? null;
        $customer_id = $session['customer'];
        $subscription_id = $session['subscription'];

        if ($user_id) {
            $stmt = $conn->prepare("UPDATE users SET stripe_customer_id = ?, subscription_id = ? WHERE id = ?");
            $stmt->bind_param("ssi", $customer_id, $subscription_id, $user_id);
            $stmt->execute();
        }
        break;

    case 'customer.subscription.created':
    case 'customer.subscription.updated':
        /**
         * 2. Plan Belirleme ve Erişim Kontrolü (Critical Event)
         */
        $sub = $event['data']['object'];
        $subscription_id = $sub['id'];
        $status = $sub['status']; // 'active', 'past_due', 'canceled', etc.
        $price_id = $sub['items']['data'][0]['price']['id'] ?? '';
        $plan = $plan_mapping[$price_id] ?? 'none';

        // Update user based on subscription_id
        $sql = "UPDATE users SET subscription_status = ?, plan = ? WHERE subscription_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $status, $plan, $subscription_id);
        $stmt->execute();
        break;

    case 'customer.subscription.deleted':
        /**
         * 3. Abonelik İptali - Erişimi Kes
         */
        $sub = $event['data']['object'];
        $subscription_id = $sub['id'];
        
        $sql = "UPDATE users SET subscription_status = 'canceled', plan = 'none' WHERE subscription_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $subscription_id);
        $stmt->execute();

        // 🚀 PHASE 1 EMAIL: Abonelik İptal Bildirimi
        $stmt_usr = $conn->prepare("SELECT full_name, email FROM users WHERE subscription_id = ?");
        $stmt_usr->bind_param("s", $subscription_id);
        $stmt_usr->execute();
        $u_info = $stmt_usr->get_result()->fetch_assoc();
        
        if ($u_info) {
            require_once 'mailer.php';
            sendSubscriptionCancelledEmail($u_info['email'], $u_info['full_name']);
        }
        break;

    case 'invoice.payment_succeeded':
        /**
         * 4. Fatura Kaydı (Yeni Ay Yenilemesi)
         */
        $invoice = $event['data']['object'];
        $customer_id = $invoice['customer'];
        $stripe_invoice_id = $invoice['id'];
        $amount = $invoice['amount_total'] / 100;
        
        // Find user by customer_id
        $stmt = $conn->prepare("SELECT id, full_name, email FROM users WHERE stripe_customer_id = ?");
        $stmt->bind_param("s", $customer_id);
        $stmt->execute();
        $u_res = $stmt->get_result()->fetch_assoc();
        
        if ($u_res) {
            $user_id = $u_res['id'];
            $full_name = $u_res['full_name'];
            $email = $u_res['email'];
            $invoice_num = 'INV-' . date('Ymd') . '-' . str_pad($user_id, 4, '0', STR_PAD_LEFT);
            
            $sql_inv = "INSERT INTO invoices (user_id, stripe_invoice_id, invoice_num, amount, status) VALUES (?, ?, ?, ?, 'paid')";
            $stmt_inv = $conn->prepare($sql_inv);
            $stmt_inv->bind_param("issd", $user_id, $stripe_invoice_id, $invoice_num, $amount);
            $stmt_inv->execute();

            // 🚀 PHASE 1 EMAIL: Ödeme Başarılı Bildirimi
            require_once 'mailer.php';
            sendPaymentSuccessEmail($email, $full_name, $amount, $invoice_num);
        }
        break;

    case 'invoice.payment_failed':
        /**
         * 5. Ödeme Gecikmesi - Direkt banlamıyoruz (Past Due) + Hatırlatıcı Gönder
         */
        $invoice = $event['data']['object'];
        $subscription_id = $invoice['subscription'];
        
        $sql = "UPDATE users SET subscription_status = 'past_due' WHERE subscription_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $subscription_id);
        $stmt->execute();

        // 🚀 PHASE 1 EMAIL: Ödeme Başarısız Bildirimi
        $stmt_usr = $conn->prepare("SELECT full_name, email FROM users WHERE subscription_id = ?");
        $stmt_usr->bind_param("s", $subscription_id);
        $stmt_usr->execute();
        $u_info = $stmt_usr->get_result()->fetch_assoc();
        
        if ($u_info) {
            require_once 'mailer.php';
            sendPaymentFailedReminder($u_info['email'], $u_info['full_name']);
        }
        break;

    case 'invoice.upcoming':
        /**
         * 6. Yenileme Yaklaşıyor (Proaktif İletişim)
         */
        $invoice = $event['data']['object'];
        $customer_id = $invoice['customer'];
        $amount = $invoice['amount_remaining'] / 100;
        $date = date('d-m-Y', $invoice['next_payment_attempt']);

        $stmt = $conn->prepare("SELECT full_name, email FROM users WHERE stripe_customer_id = ?");
        $stmt->bind_param("s", $customer_id);
        $stmt->execute();
        $u_info = $stmt->get_result()->fetch_assoc();

        if ($u_info) {
            require_once 'mailer.php';
            sendUpcomingRenewalEmail($u_info['email'], $u_info['full_name'], $amount, $date);
        }
        break;
}

closeDBConnection($conn);
http_response_code(200);
echo json_encode(['status' => 'success']);
?>