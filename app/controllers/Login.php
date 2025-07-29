<?php
// Login.php - Updated to work with your exact table structure
session_start();
// $_SESSION['name'] = 'Saman';
// echo $_SESSION['username'];

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");


// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


include_once '../models/DbConnector.php';
include_once '../models/User.php';


try {
    // Get JSON input
    $input = file_get_contents("php://input");
    $data = json_decode($input);


    // Validate input
    if (!isset($data->email) || !isset($data->password)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Email and password are required."
        ]);
        exit;
    }


    $email = trim($data->email);
    $password = $data->password;


    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Invalid email format."
        ]);
        exit;
    }


    // Database connection
    $db = new DbConnector();
    $con = $db->getConnection();


    if (!$con) {
        throw new Exception("Database connection failed");
    }


    // Create user object and attempt login
    $user = new User();
    $user->setEmail($email);
    $user->setPassword($password);


    $loggedInUser = $user->login($con);


    if ($loggedInUser) {
        // Set session variables
        $_SESSION["userId"] = $loggedInUser->getId();
        $_SESSION["role"] = $loggedInUser->getRole();


        // Return success response
        echo json_encode([
            "success" => true,
            "userId" => $loggedInUser->getId(),
            "role" => $loggedInUser->getRole(),
            "name" => $loggedInUser->getEmail(),
            "message" => "Login successful"
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "error" => "Invalid email or password."
        ]);
    }


} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Server error occurred. Please try again later."
    ]);
}
?>