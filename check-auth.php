<?php
session_start();
include 'db_connection.php'; // or use inline connection

if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    http_response_code(401);
    exit;
}

$userId = $_SESSION['user_id'];
$sessionToken = $_SESSION['session_token'];

$stmt = $conn->prepare("SELECT session_token FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['session_token'] !== $sessionToken) {
    // ❌ Session token mismatch: force logout
    session_unset();
    session_destroy();

    echo json_encode(["success" => false, "error" => "Session expired or logged in elsewhere"]);
    http_response_code(403);
    exit;
}

// ✅ Session valid
// You can now continue with protected logic
?>
