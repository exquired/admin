<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$host = 'localhost';
$dbname = 'egdb';
$username = 'root';  
$password = ''; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents("php://input"), true);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $firstName = htmlspecialchars($data['AFname']);  
        $lastName = htmlspecialchars($data['ALname']);    
        $email = htmlspecialchars($data['AEmail']);
        $password = password_hash($data['APassword'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO EADMIN (AFname, ALname, AEmail,APassword) 
                VALUES (:AFname, :ALname, :AEmail, :APassword)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':AFname', $firstName);
        $stmt->bindParam(':ALname', $lastName);
        $stmt->bindParam(':AEmail', $email);
        $stmt->bindParam(':APassword', $password);

        if ($stmt->execute()) {
            // Start a session and store user data
            $userId = $conn->lastInsertId();
            $_SESSION['user_id'] = $userId; // Store the user's ID in the session

            echo json_encode(["message" => "Registration successful!", "redirect" => "/login"]);
        } else {
            echo json_encode(["error" => "Failed to register. Try again."]);
        }
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
}
?>