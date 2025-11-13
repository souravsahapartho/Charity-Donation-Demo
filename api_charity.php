<?php
// api_charity.php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$conn = getDBConnection();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$charityId = filter_var($data['charityId'] ?? '', FILTER_SANITIZE_STRING);
$needs = filter_var($data['needs'] ?? '', FILTER_SANITIZE_STRING);

if (!$charityId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Charity ID is missing.']);
    closeDBConnection();
    exit;
}

// Update the needs field in the charities table
$sql = "UPDATE charities SET needs = ? WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $needs, $charityId);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => "Needs for {$charityId} updated successfully."]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Charity ID not found or needs were unchanged.']);
    }
} else {
    http_response_code(500);
    error_log("SQL Error: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Failed to update needs: ' . $stmt->error]);
}

$stmt->close();
closeDBConnection();
?>