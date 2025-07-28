<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Content-Type: application/json");

// Database connection
$host = "localhost";
$db = "route_pro_db";
$user = "root";
$pass = "newpassword";

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

// Fetch full driver info where user_id = ?
$sql = "SELECT name, vehicle_type, location, experience, contact FROM drivers WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row); // return entire row directly
} else {
    echo json_encode(["error" => "Driver not found"]);
}

$stmt->close();
$conn->close();
?>
