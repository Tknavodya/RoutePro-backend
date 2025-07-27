<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = "localhost";
$user = "root";
$password = "newpassword";
$dbname = "route_pro_db";
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
    exit;
}

$required = ['fullName', 'email', 'phone', 'license', 'vehicleType', 'experience', 'location', 'password'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(["success" => false, "error" => "Missing field: $field"]);
        exit;
    }
}

$name = $conn->real_escape_string($data['fullName']);
$email = $conn->real_escape_string($data['email']);
$phone = $conn->real_escape_string($data['phone']);
$license = $conn->real_escape_string($data['license']);
$vehicleType = $conn->real_escape_string($data['vehicleType']);
$experience = (int)$data['experience'];
$location = $conn->real_escape_string($data['location']);
$password = password_hash($data['password'], PASSWORD_BCRYPT);

$stmtUser = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'driver')");
$stmtUser->bind_param("ss", $email, $password);

if ($stmtUser->execute()) {
    $user_id = $stmtUser->insert_id;

    $stmtDriver = $conn->prepare("INSERT INTO drivers (user_id, name, phone, license_no, vehicle_type, experience, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmtDriver->bind_param("issssis", $user_id, $name, $phone, $license, $vehicleType, $experience, $location);

    if ($stmtDriver->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Driver insert failed: " . $stmtDriver->error]);
    }

    $stmtDriver->close();
} else {
    echo json_encode(["success" => false, "error" => "User insert failed: " . $stmtUser->error]);
}

$stmtUser->close();
$conn->close();
?>
