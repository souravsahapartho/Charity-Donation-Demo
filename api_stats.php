<?php
// api_stats.php
header('Content-Type: application/json');
require_once 'db_connect.php';

$conn = getDBConnection();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$response = [
    'success' => true,
    'stats' => [],
    'charityNeeds' => []
];

// --- 1. Get Global Statistics ---
$stats_query = "
    SELECT 
        SUM(CASE WHEN status = 'Confirmed' THEN amount ELSE 0 END) AS total_donations,
        COUNT(DISTINCT donor_id) AS total_donors,
        COUNT(donation_id) AS total_contributions
    FROM donations;
";
if ($result = $conn->query($stats_query)) {
    $response['stats'] = $result->fetch_assoc();
}

// --- 2. Get Charity Needs List ---
$needs_query = "SELECT id, name, region, needs, totalGained FROM charities ORDER BY totalGained DESC;";
if ($result = $conn->query($needs_query)) {
    while ($row = $result->fetch_assoc()) {
        // Convert totalGained from string/decimal to float for JS consistency
        $row['totalGained'] = (float)$row['totalGained']; 
        $response['charityNeeds'][] = $row;
    }
}

closeDBConnection();
echo json_encode($response);
?>