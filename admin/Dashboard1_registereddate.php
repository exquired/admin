<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "galeria";

// Create database connection
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
        // CALCULATE TOTAL CLIENTS
        $clientsQuery = "SELECT COUNT(*) AS totalClients FROM ECLIENT";
        $clientsResult = $conn->query($clientsQuery);
        if (!$clientsResult) {
            throw new Exception("Error fetching clients: " . $conn->error);
        }
        $clientsRow = $clientsResult->fetch_assoc();
        $totalClients = (int)$clientsRow['totalClients'];

        // CALCULATE TOTAL SERVICE PROVIDERS
        $providersQuery = "SELECT COUNT(*) AS totalProviders FROM SERVICEPROVIDER";
        $providersResult = $conn->query($providersQuery);
        if (!$providersResult) {
            throw new Exception("Error fetching service providers: " . $conn->error);
        }
        $providersRow = $providersResult->fetch_assoc();
        $totalProviders = (int)$providersRow['totalProviders'];

        // CALCULATE TOTAL USERS
        $totalUsers = $totalClients + $totalProviders;

        // CALCULATE MONTHLY USER REGISTRATIONS
        $monthlyUsersQuery = "
            SELECT MONTHNAME(registration_date) AS month, COUNT(*) AS users
            FROM (
                SELECT registration_date FROM ECLIENT
                UNION ALL
                SELECT registration_date FROM SERVICEPROVIDER
            ) AS combined
            WHERE YEAR(registration_date) = YEAR(CURDATE())
            GROUP BY MONTH(registration_date)
            ORDER BY MONTH(registration_date)";
        
        $monthlyUsersResult = $conn->query($monthlyUsersQuery);
        if (!$monthlyUsersResult) {
            throw new Exception("Error fetching monthly user data: " . $conn->error);
        }

        // Initialize an array with all months to ensure zero values are included
        $months = [
            "January" => 0, "February" => 0, "March" => 0, "April" => 0,
            "May" => 0, "June" => 0, "July" => 0, "August" => 0,
            "September" => 0, "October" => 0, "November" => 0, "December" => 0
        ];

        while ($row = $monthlyUsersResult->fetch_assoc()) {
            $months[$row['month']] = (int)$row['users'];
        }

        // Format monthly data for the frontend
        $monthlyUsers = [];
        foreach ($months as $month => $users) {
            $monthlyUsers[] = ["month" => $month, "users" => $users];
        }

        // Return the results as JSON
        echo json_encode([
            "success" => true,
            "totalClients" => $totalClients,
            "totalProviders" => $totalProviders,
            "totalUsers" => $totalUsers,
            "monthlyUsers" => $monthlyUsers
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
