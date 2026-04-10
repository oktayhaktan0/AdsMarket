<?php
/**
 * Google OAuth 2.0 Callback Handler
 */
header('Content-Type: application/json');
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    handleError('User not logged in', 401);
}

$user_id = $_SESSION['user_id'];
$code = $_GET['code'] ?? null;

if (!$code) {
    if (isset($_GET['error'])) {
        handleError('Google Auth Error: ' . $_GET['error']);
    }
    handleError('No authorization code provided');
}

// Exchange code for tokens
$url = 'https://oauth2.googleapis.com/token';
$data = [
    'code' => $code,
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$token_data = json_decode($response, true);

if ($http_code !== 200) {
    handleError('Failed to exchange code: ' . ($token_data['error_description'] ?? $token_data['error'] ?? 'Unknown error'));
}

$refresh_token = $token_data['refresh_token'] ?? null;
$access_token = $token_data['access_token'] ?? null;
$expires_in = $token_data['expires_in'] ?? null;
$expires_at = time() + $expires_in;

if (!$refresh_token) {
    // Note: Google only sends refresh_token on the first consent or if prompt=consent is used.
    // Since we used prompt=consent, we should ideally always get it if it's the first time or re-authenticating.
    handleError('No refresh token received. Try revoking app access and connecting again.');
}

// Update tokens AND advance onboarding to step 4 (Step 3 Finished)
$conn = getDBConnection();
$stmt = $conn->prepare("UPDATE users SET google_refresh_token = ?, google_access_token = ?, google_token_expires_at = ?, onboarding_step = GREATEST(onboarding_step, 4) WHERE id = ?");
$stmt->bind_param("ssii", $refresh_token, $access_token, $expires_at, $user_id);

if ($stmt->execute()) {
    // Redirect back to dashboard with success message
    header('Location: ../dashboard.html?auth=success');
    exit;
} else {
    handleError('Failed to save tokens to database: ' . $conn->error);
}

closeDBConnection($conn);
