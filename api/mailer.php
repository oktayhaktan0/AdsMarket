<?php
/**
 * Pro Mailer - Using SimpleSMTP for 100% Deliverability
 */
require_once 'config.php';
require_once 'SimpleSMTP.php';

function sendSystemEmail($to, $subject, $body_html)
{
    // SMTP Engine Initialization
    $smtp = new SimpleSMTP(SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS);
    
    // E-postayı gönder
    $success = $smtp->send($to, $subject, $body_html, SMTP_FROM_NAME, SMTP_FROM);
    
    // Eğer SMTP hata verirse bir kerelik mail()'e dön (Yedek Plan)
    if (!$success) {
        $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM . ">\r\n" .
                   "Content-Type: text/html; charset=UTF-8\r\n";
        return mail($to, $subject, $body_html, $headers, "-f" . SMTP_FROM);
    }
    
    return $success;
}

// Taslak Fonksiyonlar (Artık SMTP Motoruna bağlı)
function sendWelcomeEmail($email, $name, $temp_pass) {
    $subject = "Welkom bij AdsMarket.nl - Uw accountgegevens";
    $html = "<div style='font-family: Arial, sans-serif; max-width: 600px; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px;'>
        <h2 style='color: #2563eb;'>Hoi $name, Welkom bij AdsMarket! 🚀</h2>
        <p>Uw account is succesvol aangemaakt. Email: $email</p>
        <div style='background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;'>
             <p><strong>Uw login:</strong> <a href='https://demo.adsmarket.nl/login.html'>AdsMarket Dashboard</a></p>
        </div>
        <p>Met vriendelijke groet,<br>AdsMarket Team</p>
    </div>";
    return sendSystemEmail($email, $subject, $html);
}

function sendPaymentFailedReminder($email, $name) {
    $subject = "⚠ Betaling niet gelukt - AdsMarket.nl";
    $html = "<h2>Betalingsprobleem</h2><p>Hoi $name, de betaling is niet gelukt. Log in om uw betaalmethode bij te werken.</p>";
    return sendSystemEmail($email, $subject, $html);
}

function sendPaymentSuccessEmail($email, $name, $amount, $invoice_num) {
    $subject = "Betaling ontvangen - Factuur $invoice_num";
    $html = "<h2>Gelukt! 🎉</h2><p>Hoi $name, we hebben €$amount ontvangen voor factuur #$invoice_num.</p>";
    return sendSystemEmail($email, $subject, $html);
}

function sendSubscriptionCancelledEmail($email, $name) {
    $subject = "Abonnement stopgezet";
    $html = "<h2>Abonnement Geannuleerd</h2><p>Hoi $name, uw abonnement is stopgezet. We hopen u snel weer te zien.</p>";
    return sendSystemEmail($email, $subject, $html);
}

function sendUpcomingRenewalEmail($email, $name, $amount, $date) {
    $subject = "Binnenkort verlenging";
    $html = "<h2>Herinnering ⏳</h2><p>Beste $name, op $date wordt €$amount afgeschreven via uw gekoppelde betaalmethode.</p>";
    return sendSystemEmail($email, $subject, $html);
}
?>
