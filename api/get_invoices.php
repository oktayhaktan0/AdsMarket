<?php
/**
 * Get Invoices for Dashboard
 */

header('Content-Type: application/json');
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    handleError('Niet ingelogd', 401);
}

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

$sql = "SELECT invoice_num, amount, status, created_at FROM invoices WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$invoices = [];
while ($row = $result->fetch_assoc()) {
    $invoices[] = $row;
}

// Fallback: If no invoices, show one current invoice if subscription is active
if (empty($invoices)) {
    // Get current plan to show a starting invoice
    $sql_sub = "SELECT plan, status, created_at FROM subscriptions WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt_sub = $conn->prepare($sql_sub);
    $stmt_sub->bind_param("i", $user_id);
    $stmt_sub->execute();
    $sub = $stmt_sub->get_result()->fetch_assoc();

    if ($sub) {
        $amount = ($sub['plan'] === 'starter') ? 499.00 : (($sub['plan'] === 'growth') ? 999.00 : 2499.00);
        $invoices[] = [
            'invoice_num' => '#INV-' . date('Y') . '-001',
            'amount' => $amount,
            'status' => ($sub['status'] === 'active') ? 'paid' : 'pending',
            'created_at' => $sub['created_at']
        ];
    }
}

sendSuccess($invoices);
?>