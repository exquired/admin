<?php
header('Content-Type: application/json');

// Database connection

$servername = "localhost";
$username = "root";
$dbname = "egdb";
$password = "";   

$dsn = "mysql:servername=$servername;dbname=$dbname;username=$username";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get request method
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Handle the request
if ($requestMethod === 'POST') {
    // Get the action and username from the request body
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'];
    $username = $input['username'];

    if ($action === 'Restrict') {
        restrictUser ($pdo, $username);
    } elseif ($action === 'Ban') {
        banUser ($pdo, $username);
    } elseif ($action === 'Ignore') {
        ignoreUser ($pdo, $username);
    } else {
        echo json_encode(['error' => 'Invalid action']);
    }
} elseif ($requestMethod === 'GET') {
    // Fetch all reports
    fetchReports($pdo);
} else {
    echo json_encode(['error' => 'Method not allowed']);
}

// Function to restrict a user
function restrictUser ($pdo, $username) {
    $restrictedUntil = date('Y-m-d H:i:s', strtotime('+30 days'));
    $stmt = $pdo->prepare("UPDATE user_reports SET is_restricted = TRUE, restricted_until = ? WHERE username = ?");
    if ($stmt->execute([$restrictedUntil, $username])) {
        echo json_encode(['message' => "$username has been restricted until $restrictedUntil"]);
    } else {
        echo json_encode(['error' => 'Failed to restrict user']);
    }
}

// Function to ban a user
function banUser ($pdo, $username) {
    $stmt = $pdo->prepare("DELETE FROM user_reports WHERE username = ?");
    if ($stmt->execute([$username])) {
        echo json_encode(['message' => "$username has been banned"]);
    } else {
        echo json_encode(['error' => 'Failed to ban user']);
    }
}

// Function to ignore a user
function ignoreUser ($pdo, $username) {
    // Implementation for ignoring a user (could be a simple log or a status update)
    echo json_encode(['message' => "$username has been ignored"]);
}

// Function to fetch all reports
function fetchReports($pdo) {
    $stmt = $pdo->query("SELECT * FROM user_reports");
    $reports = $stmt->fetchAll();
    echo json_encode($reports);
}
?>