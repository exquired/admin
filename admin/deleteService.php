<?php
// Set headers to allow CORS and specify allowed methods and headers
header('Access-Control-Allow-Origin: http://localhost:5173'); 
header('Access-Control-Allow-Methods: DELETE'); 
header('Access-Control-Allow-Headers: Content-Type');

// Database connection parameters
$host = "localhost";
$user = "root";
$password = "";
$dbname = "galeria";

// Establish a database connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Get the input from the user
    parse_str(file_get_contents("php://input"), $input); 
    $categoryName = $input['categoryName'] ?? '';

    // Input validation
    if (empty($categoryName)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Category name cannot be empty."]);
        exit;
    }

    // Prepare and bind the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM SERVICECATEG WHERE CategName = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to prepare statement: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("s", $categoryName);

    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Category deleted successfully."]);
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Category not found."]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to delete category: " . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
} else {
    // Handle unsupported request methods
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method. Use DELETE."]);
}

// Close the database connection
$conn->close();
?>