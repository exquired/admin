<?php
header('Access-Control-Allow-Origin: http://localhost:5175');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database connection details - consider using environment variables in production
$host = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') ?: "";
$dbname = getenv('DB_NAME') ?: "galeria";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    error_log("Connection failed: " . $conn->connect_error); // Log error instead of displaying
    die(json_encode(["status" => "error", "message" => "Connection failed."]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = trim($_POST['categoryName'] ?? '');

    if (empty($categoryName)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Category name cannot be empty."]);
        exit;
    }

    // Optional: Add further validation for category name
    if (strlen($categoryName) > 255) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Category name is too long."]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO SERVICECATEG (CategName) VALUES (?)");
    $stmt->bind_param("s", $categoryName);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Category added successfully."]);
    } else {
        http_response_code(500);
        error_log("Failed to add category: " . $stmt->error); // Log error
        echo json_encode(["status" => "error", "message" => "Failed to add category."]);
    }

    $stmt->close();
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed."]);
}

$conn->close();
?>