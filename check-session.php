<?php
session_start();
include 'db_connection.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if (isset($_SESSION['user_id']) && isset($_SESSION['session_token'])) {
    $userId = $_SESSION['user_id'];
    $sessionToken = $_SESSION['session_token'];

    $query = $conn->prepare("SELECT session_token FROM users WHERE id = ?");
    $query->bind_param("i", $userId);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();

    if ($user && $user['session_token'] === $sessionToken) {
        echo json_encode([
            "loggedIn" => true,
            "userId" => $userId,
            "role" => $_SESSION['role']
        ]);
    } else {
        // Session mismatch – logout
        session_unset();
        session_destroy();
        echo json_encode(["loggedIn" => false]);
    }
} else {
    echo json_encode(["loggedIn" => false]);
}
