<?php
require_once '../models/User.php';
require_once '../models/DbConnector.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Read and decode input
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
    exit;
}

// Extract input values with fallback
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$phone = $data['phone'] ?? '';
$password = $data['password'] ?? '';

// Validate required fields
if (empty($name) || empty($email) || empty($phone) || empty($password)) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

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



    // Hash password and insert user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $role = 'traveller';
    $rating = 0;

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES ( ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $role, $rating]);
    $user_id = $conn->lastInsertId();



     // Insert into travellers table
    $stmt = $conn->prepare("INSERT INTO travellers (user_id, name, phone) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $name, $phone]);

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    echo json_encode(["success" => false, "error" => "Server error"]);
}
