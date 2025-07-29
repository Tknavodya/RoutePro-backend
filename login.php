<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 🔐 Start session
session_start();
require 'db.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
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
$query = $conn->prepare("SELECT * FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    // 🔑 Generate unique session token
    $session_token = bin2hex(random_bytes(32));

    // 💾 Save session token to DB (invalidate old sessions)
    $update = $conn->prepare("UPDATE users SET session_token = ? WHERE id = ?");
    $update->bind_param("si", $session_token, $user['id']);
    $update->execute();

    // 🧠 Store session data
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['email'] = $email;
    $_SESSION['session_token'] = $session_token;

    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "userId" => $user['id'],
        "role" => $user['role']
    ]);
} else {
    echo json_encode(["success" => false, "error" => "Invalid credentials"]);
}

$query->close();
$conn->close();
