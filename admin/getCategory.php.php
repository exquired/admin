<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Database connection settings

$servername = "localhost";
$username = "root";
$dbname = "egdb";
$password = "";   

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Fetch categories from the database
$sql = "SELECT CategoryID, CategName FROM SERVICECATEG";
if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = [
                'id' => $row['CategoryID'],
                'name' => $row['CategName']
            ];
        }
        echo json_encode($categories); // Return the categories as an array
    } else {
        echo json_encode([]); // Return empty array if no categories are found
    }
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Query failed: " . $conn->error]);
}

// Close the database connection
$conn->close();
?>
