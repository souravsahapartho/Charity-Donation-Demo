<?php
// api_donations.php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Use POST for submission.']);
    exit;
}

$conn = getDBConnection();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Sanitize and validate inputs
$charityId = filter_var($data['charityId'] ?? '', FILTER_SANITIZE_STRING);
$type = filter_var($data['type'] ?? '', FILTER_SANITIZE_STRING);
$donorId = filter_var($data['donorId'] ?? 'anonymous', FILTER_SANITIZE_STRING);
$itemsData = filter_var($data['itemsData'] ?? null, FILTER_SANITIZE_STRING);
$amount = filter_var($data['amount'] ?? 0.00, FILTER_VALIDATE_FLOAT);

if (!$charityId || !in_array($type, ['financial', 'food', 'goods'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing required fields.']);
    closeDBConnection();
    exit;
}

// Ensure non-financial types have items data, and financial types have a valid amount
if ($type !== 'financial') {
    $amount = 0.00;
    if (!$itemsData) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Item details are required for non-financial donations.']);
        closeDBConnection();
        exit;
    }
} elseif ($amount <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Financial amount must be greater than zero.']);
    closeDBConnection();
    exit;
}

// Insert into donations table (status defaults to 'Pending Confirmation')
$sql = "INSERT INTO donations (charity_id, donor_id, type, amount, items_data, status) VALUES (?, ?, ?, ?, ?, 'Pending Confirmation')";

$stmt = $conn->prepare($sql);
// 'ssds' means string, string, double, string
$stmt->bind_param("ssds", $charityId, $donorId, $type, $amount, $itemsData); 

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Donation logged successfully and is pending confirmation.']);
} else {
    http_response_code(500);
    error_log("SQL Error: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Failed to execute statement: ' . $stmt->error]);
}

$stmt->close();
closeDBConnection();
?>