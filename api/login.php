<?php
/**
 * Login API for PHP Refactored
 */

header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    handleError('Invalid request method', 405);
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    handleError('E-mailadres en wachtwoord zijn verplicht.');
}

$conn = getDBConnection();

$stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    handleError('Ongeldige e-mail of wachtwoord (E-mail niet gevonden).');
}

$user = $result->fetch_assoc();

if (password_verify($password, $user['password'])) {
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $email;
    $_SESSION['full_name'] = $user['full_name'];

    echo json_encode([
        'success' => true,
        'message' => 'Login succesvol!',
        'redirect' => 'dashboard.php' // Correctly redirecting to PHP version
    ]);
} else {
    handleError('Ongeldige e-mail of wachtwoord (Verkeerd wachtwoord).');
}

closeDBConnection($conn);
?>