<?php
/**
 * Database Setup Script for RoutePro Backend
 * Run this script once to create the necessary database tables
 */

// Database configuration
$host = "localhost";
$username = "root";
$password = "newpassword";
$database = "route_pro_db";

try {
    // Create connection
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $database");
    echo "Database '$database' created successfully.\n";
    
    // Use the database
    $pdo->exec("USE $database");
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('driver', 'guide', 'traveller', 'admin') NOT NULL,
        rating DECIMAL(3,2) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        reset_token VARCHAR(255) NULL,
        reset_token_expiry DATETIME NULL
    )";
    $pdo->exec($sql);
    echo "Table 'users' created successfully.\n";
    
    // Create drivers table
    $sql = "CREATE TABLE IF NOT EXISTS drivers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        phone VARCHAR(15) NOT NULL,
        license_no VARCHAR(50) NOT NULL,
        vehicle_type VARCHAR(50) NOT NULL,
        experience VARCHAR(100) NOT NULL,
        location VARCHAR(100) NULL,
        status ENUM('available', 'nonavailable', 'busy') DEFAULT 'nonavailable',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Table 'drivers' created successfully.\n";
    
    // Create guides table
    $sql = "CREATE TABLE IF NOT EXISTS guides (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        phone VARCHAR(15) NOT NULL,
        nic VARCHAR(20) NOT NULL,
        license_no VARCHAR(50) NOT NULL,
        experience VARCHAR(100) NOT NULL,
        location VARCHAR(100) NULL,
        languages VARCHAR(200) NOT NULL,
        status ENUM('available', 'nonavailable', 'busy') DEFAULT 'nonavailable',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Table 'guides' created successfully.\n";
    
    // Create travellers table
    $sql = "CREATE TABLE IF NOT EXISTS travellers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        phone VARCHAR(15) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Table 'travellers' created successfully.\n";
    
    // Create admins table
    $sql = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        department VARCHAR(100) NOT NULL,
        permissions VARCHAR(100) DEFAULT 'basic',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Table 'admins' created successfully.\n";
    
    // Create routes table
    $sql = "CREATE TABLE IF NOT EXISTS routes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        route_name VARCHAR(100) NOT NULL,
        start_location VARCHAR(100) NOT NULL,
        end_location VARCHAR(100) NOT NULL,
        distance DECIMAL(10,2) NOT NULL,
        estimated_duration INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Table 'routes' created successfully.\n";
    
    // Create bookings table
    $sql = "CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        traveller_id INT NOT NULL,
        route_id INT NOT NULL,
        driver_id INT NULL,
        guide_id INT NULL,
        status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
        booking_date DATE NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (traveller_id) REFERENCES travellers(id) ON DELETE CASCADE,
        FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE CASCADE,
        FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE SET NULL,
        FOREIGN KEY (guide_id) REFERENCES guides(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "Table 'bookings' created successfully.\n";
    
    // Create reviews table
    $sql = "CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id INT NOT NULL,
        reviewer_id INT NOT NULL,
        reviewee_id INT NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
        FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (reviewee_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Table 'reviews' created successfully.\n";
    
    // Insert sample admin user
    $adminEmail = "admin@routepro.com";
    $adminPassword = password_hash("admin123", PASSWORD_DEFAULT);
    
    $checkAdmin = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkAdmin->execute([$adminEmail]);
    
    if (!$checkAdmin->fetch()) {
        $insertUser = $pdo->prepare("INSERT INTO users (name, email, password, role, rating) VALUES (?, ?, ?, ?, ?)");
        $insertUser->execute(["System Admin", $adminEmail, $adminPassword, "admin", 5]);
        $adminUserId = $pdo->lastInsertId();
        
        $insertAdmin = $pdo->prepare("INSERT INTO admins (user_id, name, department, permissions) VALUES (?, ?, ?, ?)");
        $insertAdmin->execute([$adminUserId, "System Admin", "IT", "full"]);
        
        echo "Sample admin user created:\n";
        echo "Email: admin@routepro.com\n";
        echo "Password: admin123\n";
    } else {
        echo "Admin user already exists.\n";
    }
    
    echo "\n✅ Database setup completed successfully!\n";
    echo "You can now use the RoutePro Backend API.\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
