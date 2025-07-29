<?php
require 'check-auth.php'; // ✅ Validate session first

// 🧠 Safe to access protected content here
$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Example: get user info
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

echo json_encode([
    "success" => true,
    "user" => $user
]);
