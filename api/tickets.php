<?php
header('Content-Type: application/json');
require_once 'config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($subject) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Onderwerp en bericht zijn verplicht']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO tickets (user_id, subject, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $subject, $message);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Ticket succesvol aangemaakt']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database fout: ' . $conn->error]);
    }
} else {
    // GET: List tickets
    $stmt = $conn->prepare("SELECT id, subject, message, status, created_at FROM tickets WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $tickets = [];
    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $tickets]);
}

closeDBConnection($conn);
?>