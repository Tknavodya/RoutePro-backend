<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Only POST method allowed"]);
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "newpassword";
$dbname = "route_pro_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

// Set charset
$conn->set_charset("utf8mb4");

// Get JSON input
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Invalid JSON input"]);
    exit;
}

// Validate required fields
$required = ['name', 'email', 'phone', 'license_no', 'vehicle_type', 'experience', 'location', 'password'];
foreach ($required as $field) {
    if (!isset($data[$field]) || trim($data[$field]) === '') {
        echo json_encode(["success" => false, "error" => "Missing required field: $field"]);
        exit;
    }
}

// Server-side validation functions
function validateName($name) {
    return preg_match('/^[a-zA-Z\s]+$/', trim($name)) && strlen(trim($name)) >= 2;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    $cleaned = preg_replace('/[\s-]/', '', $phone);
    return preg_match('/^0\d{9}$/', $cleaned) || preg_match('/^7\d{8}$/', $cleaned);
}

function validateLicense($license) {
    return preg_match('/^[a-zA-Z0-9]+$/', $license) && strlen($license) >= 5;
}

function validatePassword($password) {
    // At least 8 chars, one letter, one number, one special char
    return preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}/', $password);
}

function validateVehicleType($type) {
    $allowed = ['car', 'minicar', 'van', 'bike', 'tuk'];
    return in_array($type, $allowed);
}

// Validate all inputs
if (!validateName($data['name'])) {
    echo json_encode(["success" => false, "error" => "Name must be at least 2 characters and contain only letters and spaces"]);
    exit;
}

if (!validateEmail($data['email'])) {
    echo json_encode(["success" => false, "error" => "Invalid email format"]);
    exit;
}

if (!validatePhone($data['phone'])) {
    echo json_encode(["success" => false, "error" => "Phone number must be 10 digits starting with 0 or 9 digits starting with 7"]);
    exit;
}

if (!validateLicense($data['license_no'])) {
    echo json_encode(["success" => false, "error" => "License number must be at least 5 characters and contain only letters and numbers"]);
    exit;
}

if (!validateVehicleType($data['vehicle_type'])) {
    echo json_encode(["success" => false, "error" => "Invalid vehicle type selected"]);
    exit;
}

if (!is_numeric($data['experience']) || $data['experience'] < 0) {
    echo json_encode(["success" => false, "error" => "Experience must be a non-negative number"]);
    exit;
}

if (!validatePassword($data['password'])) {
    echo json_encode(["success" => false, "error" => "Password must be at least 8 characters and include letters, numbers, and a special character"]);
    exit;
}

// Sanitize inputs
$name = $conn->real_escape_string(trim($data['name']));
$email = $conn->real_escape_string(trim($data['email']));
$phone = $conn->real_escape_string(preg_replace('/[\s-]/', '', $data['phone']));
$license_no = $conn->real_escape_string(trim($data['license_no']));
$vehicle_type = $conn->real_escape_string($data['vehicle_type']);
$experience = (int)$data['experience'];
$location = $conn->real_escape_string(trim($data['location']));
$password = password_hash($data['password'], PASSWORD_BCRYPT);

// Start transaction
$conn->autocommit(FALSE);

try {
    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$checkEmail) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "error" => "Email already registered"]);
        $checkEmail->close();
        $conn->rollback();
        $conn->close();
        exit;
    }
    $checkEmail->close();

    // Check if license number already exists
    $checkLicense = $conn->prepare("SELECT id FROM drivers WHERE license_no = ?");
    if (!$checkLicense) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $checkLicense->bind_param("s", $license_no);
    $checkLicense->execute();
    $result = $checkLicense->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "error" => "License number already registered"]);
        $checkLicense->close();
        $conn->rollback();
        $conn->close();
        exit;
    }
    $checkLicense->close();

    // Insert into users table
    $stmtUser = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'driver')");
    if (!$stmtUser) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmtUser->bind_param("ss", $email, $password);
    
    if (!$stmtUser->execute()) {
        throw new Exception("Failed to insert user: " . $stmtUser->error);
    }
    
    $user_id = $stmtUser->insert_id;
    $stmtUser->close();

    // Insert into drivers table
    $stmtDriver = $conn->prepare("INSERT INTO drivers (user_id, name, phone, license_no, vehicle_type, experience, location, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'available')");
    if (!$stmtDriver) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmtDriver->bind_param("issssis", $user_id, $name, $phone, $license_no, $vehicle_type, $experience, $location);
    
    if (!$stmtDriver->execute()) {
        throw new Exception("Failed to insert driver: " . $stmtDriver->error);
    }
    
    $driver_id = $stmtDriver->insert_id;
    $stmtDriver->close();

    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        "success" => true, 
        "message" => "Driver registered successfully",
        "driver_id" => $driver_id,
        "user_id" => $user_id
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Driver registration error: " . $e->getMessage());
    echo json_encode(["success" => false, "error" => "Registration failed: " . $e->getMessage()]);
} finally {
    $conn->autocommit(TRUE);
    $conn->close();
}
?>