<?php
require_once '../models/User.php';
require_once '../models/DbConnector.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
    exit;
}

$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$phone = $data['phone'] ?? '';
$license_no = $data['license_no'] ?? '';
$vehicle_type = $data['vehicle_type'] ?? '';
$experience = $data['experience'] ?? 0;
$location = $data['location'] ?? '';
$password = $data['password'] ?? '';

try {
    $db = new DbConnector();
    $conn = $db->getConnection();

    // Check for duplicate email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(["success" => false, "error" => "Email already exists"]);
        exit;
    }

    // Insert into users table
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $role = 'driver';
    $rating = 0;

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, rating) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword, $role, $rating]);
    $user_id = $conn->lastInsertId();

    // Insert into drivers table
    $stmt = $conn->prepare("INSERT INTO drivers (user_id, name, phone, license_no, vehicle_type, experience, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $name, $phone, $license_no, $vehicle_type, $experience, $location]);

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    echo json_encode(["success" => false, "error" => "Server error"]);
}
