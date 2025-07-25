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
$password = "";
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

$required = ['fullName', 'email', 'phone', 'nic', 'guideLicense', 'experience', 'location', 'languages', 'password'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(["success" => false, "error" => "Missing field: $field"]);
        exit;
    }
}

$name = $conn->real_escape_string($data['fullName']);
$email = $conn->real_escape_string($data['email']);
$phone = $conn->real_escape_string($data['phone']);
$nic = $conn->real_escape_string($data['nic']);
$license = $conn->real_escape_string($data['guideLicense']);
$experience = (int)$data['experience'];
$location = $conn->real_escape_string($data['location']);
$languages = $conn->real_escape_string($data['languages']);
$password = password_hash($data['password'], PASSWORD_BCRYPT);

$stmtUser = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'guide')");
$stmtUser->bind_param("ss", $email, $password);

if ($stmtUser->execute()) {
    $user_id = $stmtUser->insert_id;

    $stmtGuide = $conn->prepare("INSERT INTO guides (user_id, name, phone, nic, license_no, experience, location, languages) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtGuide->bind_param("issssiss", $user_id, $name, $phone, $nic, $license, $experience, $location, $languages);

    if ($stmtGuide->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to insert into guides table"]);
    }
    $stmtGuide->close();
} else {
    echo json_encode(["success" => false, "error" => "Email already registered or DB error"]);
}

$stmtUser->close();
$conn->close();
?>
