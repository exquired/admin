<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "galeria";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Set headers for API response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // CALCULATE THE CLIENT TOTAL
        $clientsQuery = "SELECT COUNT(*) AS totalClients FROM ECLIENT";
        $clientsResult = $conn->query($clientsQuery);
        if (!$clientsResult) {
            throw new Exception("Error fetching clients: " . $conn->error);
        }
        $clientsRow = $clientsResult->fetch_assoc();
        $totalClients = (int)$clientsRow['totalClients'];

        // CALCULATE THE SERPROV TOTAL
        $providersQuery = "SELECT COUNT(*) AS totalProviders FROM SERVICEPROVIDER";
        $providersResult = $conn->query($providersQuery);
        if (!$providersResult) {
            throw new Exception("Error fetching service providers: " . $conn->error);
        }
        $providersRow = $providersResult->fetch_assoc();
        $totalProviders = (int)$providersRow['totalProviders'];

        // CALCULATE TOTAL USER
        $totalUsers = $totalClients + $totalProviders;

        // 
        echo json_encode([
            "success" => true,
            "totalClients" => $totalClients,
            "totalProviders" => $totalProviders,
            "totalUsers" => $totalUsers
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
}

// Close database connection
$conn->close();
?>
