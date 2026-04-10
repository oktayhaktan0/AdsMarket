<?php
/**
 * Create Support Ticket - AdsMarket.nl
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
$data = json_decode(file_get_contents('php://input'), true);

$subject = $data['subject'] ?? '';
$message = $data['message'] ?? '';

if (empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Lütfen tüm alanları doldurun.']);
    exit;
}

$conn = getDBConnection();
$stmt = $conn->prepare("INSERT INTO support_tickets (user_id, subject, message) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $subject, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Destek talebiniz başarıyla oluşturuldu.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $conn->error]);
}

closeDBConnection($conn);
?>
