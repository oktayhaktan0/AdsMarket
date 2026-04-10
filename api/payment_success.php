<?php
/**
 * Pro Payment Success Handler
 */
require_once 'config.php';
session_start();

$session_id = $_GET['session_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('Location: ../login.html');
    exit;
}

if ($session_id) {
    $conn = getDBConnection();
    
    // Find current subscription and update user table
    $sql_check = "SELECT id, plan FROM subscriptions WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $sub_res = $stmt_check->get_result()->fetch_assoc();
    
    if ($sub_res) {
        $plan = $sub_res['plan'];
        
        // 1. UPDATE USER TABLE (Single Source of Truth)
        $sql_upd = "UPDATE users SET subscription_status = 'active', plan = ?, onboarding_step = GREATEST(onboarding_step, 2) WHERE id = ?";
        $stmt_upd = $conn->prepare($sql_upd);
        $stmt_upd->bind_param("si", $plan, $user_id);
        $stmt_upd->execute();
        
        // 2. Fatura Kaydı
        $invoice_num = 'INV-' . date('Ymd') . '-' . str_pad($user_id, 4, '0', STR_PAD_LEFT);
        $amount = ($plan === 'starter') ? 499.00 : (($plan === 'growth') ? 999.00 : 2499.00);

        $sql_inv = "INSERT INTO invoices (user_id, invoice_num, amount, status) VALUES (?, ?, ?, 'paid')";
        $stmt_inv = $conn->prepare($sql_inv);
        $stmt_inv->bind_param("isd", $user_id, $invoice_num, $amount);
        $stmt_inv->execute();
    }

    closeDBConnection($conn);
}

header('Location: ../dashboard.html?payment=success');
exit;
?>