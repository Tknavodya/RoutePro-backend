<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database connection settings
$host = "localhost";
$user = "root";
$password = "newpassword"; // default XAMPP password
$dbname = "route_pro_db";

$conn = new mysqli($host, $user, $password, $dbname);

// Check for connection error
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

// Read incoming JSON request
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (
    empty($data['fullName']) ||
    empty($data['phone']) ||
    empty($data['email']) ||
    empty($data['password'])
) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

// Sanitize input
$name = $conn->real_escape_string($data['fullName']);
$phone = $conn->real_escape_string($data['phone']);
$email = $conn->real_escape_string($data['email']);
$password = password_hash($data['password'], PASSWORD_BCRYPT); // secure hash

// Insert into users table
$stmtUser = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'traveller')");
$stmtUser->bind_param("ss", $email, $password);

if ($stmtUser->execute()) {
    $user_id = $stmtUser->insert_id;

    // Insert into travellers table
    $stmtTraveller = $conn->prepare("INSERT INTO travellers (user_id, name, phone) VALUES (?, ?, ?)");
    $stmtTraveller->bind_param("iss", $user_id, $name, $phone);

    if ($stmtTraveller->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to insert into travellers table"]);
    }

    $stmtTraveller->close();
} else {
    echo json_encode(["success" => false, "error" => "User already exists or error in user insertion"]);
}

$stmtUser->close();
$conn->close();
?>