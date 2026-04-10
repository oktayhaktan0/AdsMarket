<?php
/**
 * Register API for Faz 1 / PHP Refactored
 */

header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    handleError('Invalid request method', 405);
}

// Get and sanitize input
$company_name = $_POST['company_name'] ?? '';
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$full_name = trim($first_name . ' ' . $last_name);
$email = $_POST['email'] ?? '';
$website = $_POST['website'] ?? ''; // Optional
$google_ads_id = $_POST['google_ads_id'] ?? null;
$password = $_POST['password'] ?? '';
$plan = $_POST['plan'] ?? 'starter';

// Simple validation
if (empty($company_name) || empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
    handleError('AUB vul alle verplichte velden in.');
}

if (strlen($password) < 8) {
    handleError('Wachtwoord moet minimaal 8 tekens lang zijn.');
}

$conn = getDBConnection();

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    handleError('Dit e-mailadres is al geregistreerd.');
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Start transaction
$conn->begin_transaction();

try {
    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (company_name, full_name, email, website, google_ads_id, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $company_name, $full_name, $email, $website, $google_ads_id, $hashed_password);
    $stmt->execute();
    $user_id = $conn->insert_id;

    // Insert pending subscription
    $stmt = $conn->prepare("INSERT INTO subscriptions (user_id, plan, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("is", $user_id, $plan);
    $stmt->execute();

    $conn->commit();

    // Start Session
    session_start();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['email'] = $email;
    $_SESSION['full_name'] = $full_name;

    // 🚀 PHASE 1 EMAIL: Send Welcome Message (If functional)
    // require_once 'mailer.php';
    // sendWelcomeEmail($email, $full_name, '');

    // Response - Redirect to dashboard or payment success
    echo json_encode([
        'success' => true,
        'message' => 'Registratie succesvol!',
        'redirect' => 'dashboard.php' // Direct to dashboard or a checkout success page
    ]);

} catch (Exception $e) {
    $conn->rollback();
    handleError('Er is een fout opgetreden bij de registratie: ' . $e->getMessage());
}

closeDBConnection($conn);
?>