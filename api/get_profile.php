<?php
/**
 * Pro Get User Profile
 */
header('Content-Type: application/json');
require_once 'config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Lütfen giriş yapın.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

$sql = "SELECT id, full_name, company_name, email, website, google_ads_id, looker_studio_url, onboarding_step, subscription_status, plan FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    // If status is 'active', they have full access!
    $user['is_pro'] = ($user['subscription_status'] === 'active');
    
    // Some logic for fallback and displaying names nicely
    $user['display_status'] = [
        'active' => 'Account Actief',
        'past_due' => 'Betaling Vereist',
        'canceled' => 'Geannuleerd',
        'none' => 'Nog geen abonnement'
    ][$user['subscription_status']] ?? 'Pending';

    echo json_encode(['success' => true, 'data' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'Kullanıcı bulunamadı.']);
}

closeDBConnection($conn);
?>