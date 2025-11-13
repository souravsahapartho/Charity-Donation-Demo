<?php
// api_data_fetch.php
header('Content-Type: application/json');
require_once 'db_connect.php';

// Check for required GET parameters
$type = $_GET['type'] ?? 'stats'; // Default to fetching stats and needs
$donorId = $_GET['donorId'] ?? null;

$conn = getDBConnection();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$response = ['success' => true, 'data' => []];
$sql = '';

switch ($type) {
    case 'stats':
        // Fetch Global Statistics and All Charity Needs
        // 1. Global Stats
        $stats_query = "
            SELECT 
                SUM(CASE WHEN status = 'Confirmed' THEN amount ELSE 0 END) AS total_donations,
                COUNT(DISTINCT donor_id) AS total_donors,
                COUNT(donation_id) AS total_contributions
            FROM donations;
        ";
        $result = $conn->query($stats_query);
        $response['stats'] = $result ? $result->fetch_assoc() : [];

        // 2. Charity Needs (including data needed for login check)
        $needs_query = "SELECT id, name, region, needs, totalGained, password FROM charities ORDER BY totalGained DESC;";
        $needs_result = $conn->query($needs_query);
        $charity_needs = [];
        if ($needs_result) {
            while ($row = $needs_result->fetch_assoc()) {
                $row['totalGained'] = (float)$row['totalGained']; 
                $charity_needs[] = $row;
            }
        }
        $response['charityNeeds'] = $charity_needs;
        break;

    case 'pending':
        // Fetch Pending Donations (for Admin View)
        $sql = "SELECT * FROM donations WHERE status = 'Pending Confirmation' ORDER BY created_at DESC;";
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $response['donations'][] = $row;
            }
        }
        break;

    case 'audit':
        // Fetch Confirmed History (for Admin View)
        $sql = "SELECT * FROM donations WHERE status = 'Confirmed' ORDER BY confirmed_at DESC;";
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $response['donations'][] = $row;
            }
        }
        break;

    case 'my_donations':
        // Fetch Donor's History
        if ($donorId) {
            // NOTE: Using prepared statement for security against user-provided ID
            $stmt = $conn->prepare("SELECT * FROM donations WHERE donor_id = ? ORDER BY created_at DESC;");
            $stmt->bind_param("s", $donorId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $response['donations'][] = $row;
            }
            $stmt->close();
        } else {
            $response['success'] = false;
            $response['message'] = 'Donor ID required for fetching history.';
        }
        break;

    default:
        $response['success'] = false;
        $response['message'] = 'Invalid data fetch type.';
        break;
}

closeDBConnection();
echo json_encode($response);
?>