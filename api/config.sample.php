<?php
/**
 * Database Configuration (Sample)
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'YOUR_DB_USER');
define('DB_PASS', 'YOUR_DB_PASSWORD');
define('DB_PORT', 3306);
define('DB_NAME', 'YOUR_DB_NAME');

// Google Ads API Configuration
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
define('GOOGLE_DEVELOPER_TOKEN', 'YOUR_DEVELOPER_TOKEN');
define('GOOGLE_REDIRECT_URI', 'https://yourdomain.com/api/auth_callback.php');

// Stripe Configuration
define('STRIPE_SECRET_KEY', 'sk_test_..._YOUR_STRIPE_KEY');
define('STRIPE_WEBHOOK_SECRET', 'whsec_..._YOUR_WEBHOOK_SECRET');
define('BASE_URL', 'https://yourdomain.com');

// SMTP Configuration
define('SMTP_HOST', 'smtp.yourhost.com');
define('SMTP_USER', 'your@email.com');
define('SMTP_PASS', 'YOUR_EMAIL_PASSWORD');
define('SMTP_PORT', 465);
define('SMTP_FROM', 'your@email.com');
define('SMTP_FROM_NAME', 'AdsMarket.nl Support');

/**
 * Shared functions remain same
 */
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if ($conn->connect_error) { handleError('Database verbinding mislukt: ' . $conn->connect_error); }
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) { handleError('Database fout: ' . $e->getMessage()); }
}

function closeDBConnection($conn) { if ($conn) { $conn->close(); } }

function handleError($message, $code = 500) {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

function sendSuccess($data = null, $message = 'Success') {
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data]);
    exit;
}
?>
    
