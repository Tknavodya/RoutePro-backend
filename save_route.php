<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "route_pro_db");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$start = $data['start_location'] ?? null;
$end = $data['end_location'] ?? null;
$distance = $data['distance_km'] ?? null;
$duration = $data['estimated_time'] ?? null;

if (!$start || !$end || !$distance || !$duration) {
    echo json_encode(["success" => false, "error" => "Missing required fields."]);
    exit;
}

$sql = "INSERT INTO routes (start_location, end_location, distance_km, estimated_time)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssds", $start, $end, $distance, $duration);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
