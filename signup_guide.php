<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database credentials
$host = "localhost";
$user = "root";
$password = "newpassword"; // XAMPP default
$dbname = "route_pro_db";

// Connect to DB
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

// Read JSON body from request
$data = json_decode(file_get_contents("php://input"), true);




// Validate input
if (
    empty($data['fullName']) ||
    empty($data['phone']) ||
    empty($data['email']) ||
    empty($data['guidelicense']) ||
    empty($data['experience']) ||
    empty($data['location']) ||
    empty($data['language']) ||
    empty($data['password'])||
    empty($data['confirmPassword'])
) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

// Sanitize input

$name = $conn->real_escape_string($data['name']);
$phone = $conn->real_escape_string($data['phone']);
$email = $conn->real_escape_string($data['email']);
$nic = $conn->real_escape_string($data['nic']);
$license_no = $conn->real_escape_string($data['license_no']);
$experience = intval($data['experience']);
$location = $conn->real_escape_string($data['location']);
$languages = $conn->real_escape_string($data['languages']);
$password = password_hash($data['password'], PASSWORD_BCRYPT);


// Insert into users table
$stmtUser = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'guide')");
$stmtUser->bind_param("ss", $email, $password);
if ($stmtUser->execute()) {
    $user_id = $stmtUser->insert_id;


 // Insert into travellers table
    $stmtGuider = $conn->prepare("INSERT INTO guides (user_id, name, phone,nic,license_no,experience,location,language) VALUES (?, ?, ?,?,?,?,?)");
    $stmtGuider->bind_param("iss", $user_id, $name, $phone);

    if ($stmtGuider->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to insert into Guiders table"]);
    }

    $stmtGuider->close();
} else {
    echo json_encode(["success" => false, "error" => "User already exists or error in user insertion"]);
}

$stmtUser->close();
$conn->close();
?>









const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setForm({ ...form, [name]: type === 'checkbox' ? checked : value });
  };


const handleSubmit = async (e) => {
    e.preventDefault();

    if (!form.agree) {
      alert('Please agree to the terms and conditions');
      return;
    }

    if (form.password !== form.confirmPassword) {
      alert('Passwords do not match!');
      return;
    }

const payload = {
      name: form.fullName,
      email: form.email,
      phone: form.phone,
      nic: form.nic,
      license_no: form.guideLicense,
      experience: form.experience,
      location: form.location,
      languages: form.languages,
      password: form.password,
    };





// Validate required fields
$required = ['name', 'email', 'phone', 'password', 'nic', 'license_no', 'experience', 'location', 'languages'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(["success" => false, "error" => "Missing field: $field"]);
        exit;
    }
}

// Sanitize & prepare data
$name = $conn->real_escape_string($data['name']);
$phone = $conn->real_escape_string($data['phone']);
$email = $conn->real_escape_string($data['email']);
$nic = $conn->real_escape_string($data['nic']);
$license_no = $conn->real_escape_string($data['license_no']);
$experience = intval($data['experience']);
$location = $conn->real_escape_string($data['location']);
$languages = $conn->real_escape_string($data['languages']);
$passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

// Step 1: Insert into users table
$stmtUser = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'guider')");
$stmtUser->bind_param("ss", $email, $passwordHash);

if ($stmtUser->execute()) {
    $user_id = $stmtUser->insert_id;

    // Step 2: Insert into guiders table
    $stmtGuider = $conn->prepare("INSERT INTO guiders (user_id, name, phone, status, nic, license_no, experience, location, languages) VALUES (?, ?, ?, 'nonavailable', ?, ?, ?, ?, ?)");
    $stmtGuider->bind_param("isssssis", $user_id, $name, $phone, $nic, $license_no, $experience, $location, $languages);

    if ($stmtGuider->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to insert into guiders table"]);
    }

    $stmtGuider->close();
} else {
    echo json_encode(["success" => false, "error" => "User already exists or error inserting user"]);
}

$stmtUser->close();
$conn->close();
?>
