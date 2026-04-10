<?php
/**
 * Redirects user to Google OAuth 2.0 Consent Screen
 */
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit;
}

$params = [
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'https://www.googleapis.com/auth/adwords',
    'access_type' => 'offline',
    'prompt' => 'consent'
];

$authUrl = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query($params);

header('Location: ' . $authUrl);
exit;
