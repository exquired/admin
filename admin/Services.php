<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "egdb";

// Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Pagination and search parameters
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$searchQuery = isset($_GET['search']) ? '%' . $conn->real_escape_string($_GET['search']) . '%' : '%';

// Query for total count with search filtering
$countQuery = "
    SELECT COUNT(*) as total
    FROM SERVICES s
    LEFT JOIN SERVICEPROVIDER sp ON s.ServiceProviderID = sp.ServiceProviderID
    LEFT JOIN SERVICECATEG c ON s.CategoryID = c.CategoryID
    WHERE s.SName LIKE ?
";

$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param("s", $searchQuery);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalCount = $countResult ? (int)$countResult->fetch_assoc()['total'] : 0;

// Query for services with CategoryName, with search filtering
$sql = "
    SELECT 
        s.ServicesID AS services_id,
        s.SName AS service_name,
        COUNT(DISTINCT sp.ServiceProviderID) AS num_providers,
        c.CategName AS category_name
    FROM SERVICES s
    LEFT JOIN SERVICEPROVIDER sp ON s.ServiceProviderID = sp.ServiceProviderID
    LEFT JOIN SERVICECATEG c ON s.CategoryID = c.CategoryID
    WHERE s.SName LIKE ?
    GROUP BY s.ServicesID, s.SName, c.CategName
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $searchQuery, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = [
            'services_id' => (int)$row['services_id'],
            'service_name' => $row['service_name'],
            'num_providers' => (int)$row['num_providers'],
            'category_name' => $row['category_name']
        ];
    }
    echo json_encode([
        'total' => $totalCount,
        'services' => $services
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to execute SQL query']);
}

$conn->close();
?>
