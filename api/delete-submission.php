<?php
/**
 * Delete Submission API Endpoint
 * Deletes a form submission from database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

// Allow both DELETE and POST methods
if (!in_array($_SERVER['REQUEST_METHOD'], ['DELETE', 'POST'])) {
    handleError('Only DELETE or POST requests are allowed', 405);
}

// Get submission ID
$id = null;

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
} else {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($input['id']) ? intval($input['id']) : null;
}

if (!$id) {
    handleError('Submission ID is required', 400);
}

// Connect to database
$conn = getDBConnection();

// Prepare SQL statement
$stmt = $conn->prepare("DELETE FROM form_submissions WHERE id = ?");

if (!$stmt) {
    closeDBConnection($conn);
    handleError('Database error: ' . $conn->error, 500);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    closeDBConnection($conn);

    if ($affected_rows > 0) {
        sendSuccess(null, 'Submission deleted successfully');
    } else {
        handleError('Submission not found', 404);
    }
} else {
    $error = $stmt->error;
    $stmt->close();
    closeDBConnection($conn);
    handleError('Failed to delete submission: ' . $error, 500);
}
?>