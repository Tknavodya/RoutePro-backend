<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 🔐 Start session
session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// DB Connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "route_pro_db";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

// Read incoming data
$data = json_decode(file_get_contents("php://input"), true);
if (!$data || empty($data['email']) || empty($data['password'])) {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
    exit;
}

$email = $conn->real_escape_string($data['email']);
$password = $data['password'];

// Check if user exists
$query = "SELECT id, password, role FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user['password'])) {
        // ✅ Store in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['email'] = $email;

        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "userId" => $user['id'],
            "role" => $user['role']
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "Invalid credentials"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "User not found"]);
}

$stmt->close();
$conn->close();
?>
