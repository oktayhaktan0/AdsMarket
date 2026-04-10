<?php
/**
 * Submit Form API Endpoint
 * Handles form submissions and saves to database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    handleError('Only POST requests are allowed', 405);
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['name', 'email', 'company'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        handleError("Field '$field' is required", 400);
    }
}

// Validate email
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    handleError('Invalid email address', 400);
}

// Sanitize inputs
$name = htmlspecialchars(trim($input['name']), ENT_QUOTES, 'UTF-8');
$email = filter_var(trim($input['email']), FILTER_SANITIZE_EMAIL);
$phone = isset($input['phone']) ? htmlspecialchars(trim($input['phone']), ENT_QUOTES, 'UTF-8') : null;
$company = htmlspecialchars(trim($input['company']), ENT_QUOTES, 'UTF-8');
$budget = isset($input['budget']) ? htmlspecialchars(trim($input['budget']), ENT_QUOTES, 'UTF-8') : null;
$service = isset($input['service']) ? htmlspecialchars(trim($input['service']), ENT_QUOTES, 'UTF-8') : null;
$message = isset($input['message']) ? htmlspecialchars(trim($input['message']), ENT_QUOTES, 'UTF-8') : null;

// Get client info
$ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

// Connect to database
$conn = getDBConnection();

// Prepare SQL statement
$stmt = $conn->prepare("INSERT INTO form_submissions (name, email, phone, company, budget, service, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    closeDBConnection($conn);
    handleError('Database error: ' . $conn->error, 500);
}

// Bind parameters
$stmt->bind_param("sssssssss", $name, $email, $phone, $company, $budget, $service, $message, $ip_address, $user_agent);

// Execute statement
if ($stmt->execute()) {
    $submission_id = $stmt->insert_id;

    // Optional: Send email notification
    // sendEmailNotification($email, $name, $company);

    $stmt->close();
    closeDBConnection($conn);

    sendSuccess([
        'id' => $submission_id,
        'message' => 'Form submitted successfully'
    ], 'Bedankt voor uw bericht! We nemen binnen 24 uur contact met u op.');
} else {
    $error = $stmt->error;
    $stmt->close();
    closeDBConnection($conn);
    handleError('Failed to save submission: ' . $error, 500);
}

// Optional: Email notification function
function sendEmailNotification($to, $name, $company)
{
    $subject = "Nieuwe contactformulier inzending - AdsMarket";
    $message = "Nieuwe inzending ontvangen van:\n\n";
    $message .= "Naam: $name\n";
    $message .= "Bedrijf: $company\n";
    $message .= "Email: $to\n";

    $headers = "From: noreply@adsmarket.nl\r\n";
    $headers .= "Reply-To: info@adsmarket.nl\r\n";

    // Uncomment to enable email notifications
    // mail('info@adsmarket.nl', $subject, $message, $headers);
}
?>