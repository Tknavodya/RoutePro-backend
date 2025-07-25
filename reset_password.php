<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

$host = "localhost";
$user = "root";
$password = "";
$dbname = "route_pro_db";
$conn = new mysqli($host, $user, $password, $dbname);

$data = json_decode(file_get_contents("php://input"), true);
$token = $conn->real_escape_string($data["token"] ?? "");
$newPassword = $data["password"] ?? "";

if (!$token || !$newPassword) {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit;
}

$result = $conn->query("SELECT id, reset_token_expiry FROM users WHERE reset_token = '$token'");
if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Invalid token."]);
    exit;
}

$row = $result->fetch_assoc();
if (strtotime($row["reset_token_expiry"]) < time()) {
    echo json_encode(["success" => false, "message" => "Token expired."]);
    exit;
}

$hashed = password_hash($newPassword, PASSWORD_BCRYPT);
$userId = $row["id"];

$conn->query("UPDATE users SET password='$hashed', reset_token=NULL, reset_token_expiry=NULL WHERE id=$userId");

echo json_encode(["success" => true, "message" => "Password reset successful."]);
