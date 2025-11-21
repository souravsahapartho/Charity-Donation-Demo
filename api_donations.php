<?php
// api_donations.php
header('Content-Type: application/json');
require_once 'db_connect.php';

// 1. ALLOW FORM DATA INSTEAD OF JSON
$charityId = $_POST['charityId'] ?? '';
$type      = $_POST['type'] ?? '';
$donorId   = $_POST['donorId'] ?? 'anonymous';
$itemsData = $_POST['itemsData'] ?? null;
$amount    = $_POST['amount'] ?? 0.00;

// 2. CONNECT
$conn = getDBConnection();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// 3. VALIDATION
if (!$charityId || !in_array($type, ['financial', 'food', 'goods'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing required fields (POST).']);
    closeDBConnection();
    exit;
}

// 4. LOGIC
if ($type !== 'financial') {
    $amount = 0.00;
    if (!$itemsData) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Item details are required.']);
        closeDBConnection();
        exit;
    }
} elseif ($amount <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Financial amount must be greater than zero.']);
    closeDBConnection();
    exit;
}

// 5. INSERT
$sql = "INSERT INTO donations (charity_id, donor_id, type, amount, items_data, status) VALUES (?, ?, ?, ?, ?, 'Pending Confirmation')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssds", $charityId, $donorId, $type, $amount, $itemsData); 

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Donation logged successfully!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'SQL Error: ' . $stmt->error]);
}

$stmt->close();
closeDBConnection();
?>