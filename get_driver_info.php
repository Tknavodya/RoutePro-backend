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

// Fetch multiple fields from drivers table
$sql = "SELECT name, phone, status, license_no, vehicle_type, experience, location FROM drivers WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "name" => $row['name'],
        "phone" => $row['phone'],
        "status" => $row['status'],
        "license_no" => $row['license_no'],
        "vehicle_type" => $row['vehicle_type'],
        "experience" => $row['experience'],
        "location" => $row['location']
    ]);
} else {
    echo json_encode(["error" => "Driver not found"]);
}

$stmt->close();
$conn->close();
?>
