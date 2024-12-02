<?php
$servername = "localhost";
$username = "root";
$dbname = "galeria";
$password = "";

header('Access-Control-Allow-Origin: *');  
header('Content-Type: application/json'); 

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT 
        c.CategoryID AS id,
        c.CategName AS name,
        COUNT(DISTINCT s.ServicesID) AS num_services, 
        COUNT(DISTINCT sp.ServiceProviderID) AS num_professionals
        FROM SERVICECATEG c
        LEFT JOIN SERVICES s ON c.CategoryID = s.CategoryID
        LEFT JOIN SERVICEPROVIDER sp ON s.ServiceProviderID = sp.ServiceProviderID
        GROUP BY c.CategoryID, c.CategName";


$result = $conn->query($sql);


if ($result->num_rows > 0) {
    $categories = [];
    while($row = $result->fetch_assoc()) {
        $categories[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'num_services' => $row['num_services'],
            'num_professionals' => $row['num_professionals']
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($categories);
} else {
    echo json_encode([]);
}
$conn->close();
?>
