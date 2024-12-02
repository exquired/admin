<?php
// Allow requests from all origins (replace '*' with specific origin if needed)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database connection settings
$host = "localhost";
$user = "root";
$password = "";
$dbname = "galeria";

$conn = new mysqli($host, $user, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    sendErrorResponse(500, "Connection failed: " . $conn->connect_error);
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Read JSON input
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Check if the data is valid JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendErrorResponse(400, "Invalid JSON format.");
    }

    // Get and sanitize input data
    $serviceName = trim($inputData['serviceName'] ?? '');
    $selectedCategoryId = $inputData['selectedCategoryId'] ?? '';

    // Input validation
    if (empty($serviceName) || empty($selectedCategoryId)) {
        sendErrorResponse(400, "All fields are required.");
    }

    // Check if the service already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM SERVICES WHERE SName = ? AND CategoryID = ?");
    if ($stmt === false) {
        sendErrorResponse(500, "Service already exists: " . $conn->error);
    }

    $stmt->bind_param("si", $serviceName, $selectedCategoryId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        sendErrorResponse("Service already exists in the selected category."); // 409 Conflict
    }

    // Prepare and bind the SQL statement to insert the service
    $stmt = $conn->prepare("INSERT INTO SERVICES (SName, CategoryID) VALUES (?, ?)");
    if ($stmt === false) {
        sendErrorResponse(500, "Failed to prepare SQL statement: " . $conn->error);
    }
    
    $stmt->bind_param("si", $serviceName, $selectedCategoryId);

    // Execute the statement and handle the response
    if ($stmt->execute()) {
        sendSuccessResponse("Service added successfully.");
    } else {
        sendErrorResponse(500, "Failed to add service: " . $stmt->error);
    }

    // Close the statement
    $stmt->close();
} else {
    // Method not allowed if not POST
    sendErrorResponse(405, "Method not allowed.");
}

// Close the database connection
$conn->close();

// Helper functions for sending responses
function sendErrorResponse($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(["status" => "error", "message" => $message]);
    exit;
}

function sendSuccessResponse($message) {
    http_response_code(200);
    echo json_encode(["status" => "success", "message" => $message]);
}