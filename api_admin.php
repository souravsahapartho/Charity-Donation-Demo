<?php
// api_admin.php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Use POST for admin actions.']);
    exit;
}

$conn = getDBConnection();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? null;
$donationId = filter_var($data['donationId'] ?? null, FILTER_VALIDATE_INT);

if ($action === 'confirm' && $donationId) {
    // 1. Get the current donation details (needed for amount/type/charityId)
    $stmt_fetch = $conn->prepare("SELECT type, amount, charity_id FROM donations WHERE donation_id = ? AND status = 'Pending Confirmation'");
    $stmt_fetch->bind_param("i", $donationId);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    $donation = $result->fetch_assoc();
    $stmt_fetch->close();

    if (!$donation) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Pending donation not found or already confirmed.']);
        closeDBConnection();
        exit;
    }

    $conn->begin_transaction();
    try {
        // 2. Update the donation status to 'Confirmed'
        $stmt_update_donation = $conn->prepare("UPDATE donations SET status = 'Confirmed', confirmed_at = NOW() WHERE donation_id = ?");
        $stmt_update_donation->bind_param("i", $donationId);
        $stmt_update_donation->execute();
        
        // 3. Update charity's totalGained if it was a financial donation
        if ($donation['type'] === 'financial') {
            $stmt_update_charity = $conn->prepare("UPDATE charities SET totalGained = totalGained + ? WHERE id = ?");
            $amount = (float)$donation['amount'];
            $stmt_update_charity->bind_param("ds", $amount, $donation['charity_id']); // 'd' for double
            $stmt_update_charity->execute();
            $stmt_update_charity->close();
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => "Donation ID {$donationId} confirmed and audit logged."]);

    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        error_log("Transaction failed: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => "Confirmation failed: {$e->getMessage()}"]);
    }

    $stmt_update_donation->close();

} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action or missing ID.']);
}

closeDBConnection();
?>