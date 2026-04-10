<?php
/**
 * Get Submissions API Endpoint
 * Retrieves form submissions from database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once 'config.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    handleError('Only GET requests are allowed', 405);
}

// Get query parameters
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

// Connect to database
$conn = getDBConnection();

// Build query
$where_clauses = [];
$params = [];
$types = '';

if ($status && in_array($status, ['new', 'contacted', 'converted', 'closed'])) {
    $where_clauses[] = "status = ?";
    $params[] = $status;
    $types .= 's';
}

if ($search) {
    $search_term = "%$search%";
    $where_clauses[] = "(name LIKE ? OR email LIKE ? OR company LIKE ? OR message LIKE ?)";
    $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
    $types .= 'ssss';
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM form_submissions $where_sql";
$count_stmt = $conn->prepare($count_sql);

if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total = $count_result->fetch_assoc()['total'];
$count_stmt->close();

// Get submissions
$sql = "SELECT id, name, email, phone, company, budget, service, message, ip_address, created_at, status 
        FROM form_submissions 
        $where_sql 
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    closeDBConnection($conn);
    handleError('Database error: ' . $conn->error, 500);
}

// Add limit and offset to params
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$submissions = [];
while ($row = $result->fetch_assoc()) {
    $submissions[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'email' => $row['email'],
        'phone' => $row['phone'],
        'company' => $row['company'],
        'budget' => $row['budget'],
        'service' => $row['service'],
        'message' => $row['message'],
        'ip_address' => $row['ip_address'],
        'timestamp' => $row['created_at'],
        'status' => $row['status']
    ];
}

$stmt->close();
closeDBConnection($conn);

sendSuccess([
    'submissions' => $submissions,
    'total' => $total,
    'limit' => $limit,
    'offset' => $offset
]);
?>