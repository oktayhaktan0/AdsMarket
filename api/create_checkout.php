<?php
/**
 * Create Stripe Checkout Session
 */
header('Content-Type: application/json');
require_once 'config.php';

session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Lütfen önce giriş yapın.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan = $_POST['plan'] ?? 'starter';
    $user_id = $_SESSION['user_id'];
    $user_email = $_SESSION['email'] ?? '';

    // Stripe Fiyat ID'leri (Kendi Stripe Dashboard'undan aldığın ID'lerle değiştir)
    $prices = [
        'starter' => 'price_1TGgnCHfPSXm7DZTvE3hrHvv',
        'growth' => 'price_1TGgmzHfPSXm7DZTzy1AD2fX',
        'enterprise' => 'price_1TGgnPHfPSXm7DZT0JB9M8lX'
    ];

    // Stripe API Call (CURL)
    $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY . ':');
    curl_setopt($ch, CURLOPT_POST, true);

    $payload = [
        'success_url' => BASE_URL . '/api/payment_success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => BASE_URL . '/dashboard.html?payment=cancel',
        'payment_method_types[]' => 'card',
        'mode' => 'subscription',
        'customer_email' => $user_email,
        'metadata[user_id]' => $user_id, // Webhook'ta eşleşme için kritik
        'metadata[plan]' => $plan,
        'line_items[0][price]' => $prices[$plan],
        'line_items[0][quantity]' => 1
    ];

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));

    $result = curl_exec($ch);
    $response = json_decode($result, true);

    if (isset($response['url'])) {
        // Abonelik bilgisini önce 'pending' olarak veritabanına kaydedelim (Eğer yoksa)
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO subscriptions (user_id, plan, status) VALUES (?, ?, 'pending') ON DUPLICATE KEY UPDATE plan = ?, updated_at = CURRENT_TIMESTAMP");
        $stmt->bind_param("iss", $user_id, $plan, $plan);
        $stmt->execute();
        closeDBConnection($conn);

        echo json_encode(['success' => true, 'url' => $response['url']]);
    } else {
        $error = $response['error']['message'] ?? 'Stripe bağlantı hatası oluştu.';
        echo json_encode(['success' => false, 'message' => $error]);
    }

    if (curl_errno($ch)) {
        echo json_encode(['success' => false, 'message' => 'CURL Hatası: ' . curl_error($ch)]);
    }
    curl_close($ch);
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu.']);
}
?>