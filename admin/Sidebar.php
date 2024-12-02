<?php
// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

session_start(); // Start or resume the session

// Database connection parameters
$servername = getenv('DB_SERVER') ?: "localhost"; // Use environment variable or default
$username = getenv('DB_USERNAME') ?: "root"; // Use environment variable or default
$password = getenv('DB_PASSWORD') ?: ""; // Use environment variable or default
$dbname = getenv('DB_NAME') ?: "egdb"; // Use environment variable or default

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Get the user ID from the query parameter and validate it
$id = isset($_GET['id']) ? intval($_GET['id']) : 0; 

if ($id <= 0) {
    echo json_encode(['error' => 'Invalid user ID.']);
    exit;
}

// Prepare the SQL statement
$stmt = $conn->prepare("SELECT AFName, ALName FROM EADMIN WHERE AdminID = ?");

// Check if the statement was prepared successfully
if (!$stmt) {
    echo json_encode(['error' => 'SQL statement preparation failed: ' . $conn->error]);
    exit;
}

// Bind the user ID parameter
$stmt->bind_param("i", $id); // "i" indicates that the parameter is an integer

// Execute the statement
$stmt->execute();
$result = $stmt->get_result();

// Check if any rows were returned
if ($result->num_rows > 0) {
    // Output data of the user
    $user = $result->fetch_assoc();
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'No user found']);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>