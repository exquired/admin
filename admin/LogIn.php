<?php
// Allow requests from any origin (adjust for production)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Start session
session_start();

// Database connection
$host = 'localhost';
$dbname = 'egdb';
$db_username = 'root'; 
$db_password = ''; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the POST data
    $data = json_decode(file_get_contents("php://input"), true);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Collect form data
        $email = htmlspecialchars($data['AEmail']);
        $password = $data['APassword'];

        // Prepare SQL query to check for existing user
        $stmt = $conn->prepare("SELECT * FROM EADMIN WHERE email = :AEmail");
        $stmt->bindParam(':AEmail', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and password matches
        if ($user && password_verify($password, $user['APassword'])) {
            // Set session variables after successful login
            $_SESSION['user_id'] = $user['AdminID'];  // Store the user ID
            $_SESSION['name'] = $user['AFname'] . ' ' . $user['ALname'];  // Store the user's full name
            $_SESSION['email'] = $user['AEmail'];  // Store the user's email
            echo json_encode(["message" => "Login successful!", "redirect" => "/dashboard"]);
        } else {
            echo json_encode(["error" => "Invalid email or password."]);
        }
        
    }
} catch (PDOException $e) {
    // Handle connection failure
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
}
?>