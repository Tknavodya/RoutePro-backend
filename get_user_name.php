<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Content-Type: application/json");

// Database connection
$host = "localhost";
$db = "route_pro_db";
$user = "root";      // Change if needed
$pass = "newpassword"; // Change if you have a DB password

// Get user ID from GET request
$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;

if ($userId <= 0) {
    echo json_encode(["error" => "Invalid user ID"]);
    exit;
}

// Connect to DB
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed"]);
    exit;
}

// Fetch user name from drivers table where user_id = ?
$sql = "SELECT name FROM drivers WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["message" => $row['name']]);
} else {
    echo json_encode(["error" => "Driver not found"]);
}

$stmt->close();
$conn->close();
?>
