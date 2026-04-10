<?php
/**
 * Get User Support Tickets - AdsMarket.nl
 */
header('Content-Type: application/json');
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

$sql = "SELECT id, subject, message, status, created_at FROM support_tickets WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$tickets = [];
while ($row = $result->fetch_assoc()) {
    $row['display_status'] = [
        'open' => 'Open',
        'responded' => 'Beantwoord',
        'closed' => 'Gesloten'
    ][$row['status']] ?? $row['status'];
    $tickets[] = $row;
}

echo json_encode(['success' => true, 'data' => $tickets]);

closeDBConnection($conn);
?>
