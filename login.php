<?php
// Enable error reporting (for development only, disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle CORS headers here — must be before any output including session_start()
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// Handle preflight OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Start session before sending any output
session_start();

// Require database connection or setup here if needed
// require 'db.php'; // Commented out since you instantiate below manually


// DB connection parameters
$host = "localhost";
$user = "root";
$password = "";
$dbname = "route_pro_db";

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

// Retrieve and decode JSON POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (
    !$data || 
    empty($data['email']) || 
    empty($data['password'])
) {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
    exit;
}

// Escape and sanitize email input
$email = $conn->real_escape_string($data['email']);
$password = $data['password'];

// Prepare and execute user query
$query = $conn->prepare("SELECT * FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    // Authentication successful

    // Generate a strong unique session token (used as server-side validity)
    $reset_token = bin2hex(random_bytes(32));

    // Update the session token in the database (invalidate older sessions)
    $update = $conn->prepare("UPDATE users SET reset_token = ? WHERE id = ?");
    $update->bind_param("si", $reset_token, $user['id']);
    $update->execute();

    // Store user data and session token in PHP session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['email'] = $email;
    $_SESSION['reset_token'] = $reset_token;

    // Send success response to client
    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "userId" => $user['id'],
        "role" => $user['role']
    ]);
} else {
    // Invalid credentials
    echo json_encode(["success" => false, "error" => "Invalid credentials"]);
}

// Clean up
$query->close();
$conn->close();
