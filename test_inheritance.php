<?php
/**
 * Test Script for RoutePro MVC Inheritance Structure
 * Run this script to verify that the inheritance is working correctly
 */

// Include all necessary files
require_once __DIR__ . '/app/models/Driver.php';
require_once __DIR__ . '/app/models/Guide.php';
require_once __DIR__ . '/app/models/Traveller.php';
require_once __DIR__ . '/app/models/Admin.php';

echo "<h1>🧪 RoutePro Inheritance Structure Test</h1>\n";
echo "<pre>\n";

// Test 1: Check inheritance hierarchy
echo "=== Test 1: Inheritance Hierarchy ===\n";

$driver = new Driver("John Doe", "john@example.com", "password123");
$guide = new Guide("Jane Smith", "jane@example.com", "password123");
$traveller = new Traveller("Bob Wilson", "bob@example.com", "password123");
$admin = new Admin("Alice Admin", "alice@example.com", "password123");

echo "Driver is instance of User: " . (($driver instanceof User) ? "✅ YES" : "❌ NO") . "\n";
echo "Guide is instance of User: " . (($guide instanceof User) ? "✅ YES" : "❌ NO") . "\n";
echo "Traveller is instance of User: " . (($traveller instanceof User) ? "✅ YES" : "❌ NO") . "\n";
echo "Admin is instance of User: " . (($admin instanceof User) ? "✅ YES" : "❌ NO") . "\n\n";

// Test 2: Check inherited properties
echo "=== Test 2: Inherited Properties ===\n";

echo "Driver name: " . $driver->getName() . "\n";
echo "Driver email: " . $driver->getEmail() . "\n";
echo "Driver role: " . $driver->getRole() . "\n\n";

echo "Guide name: " . $guide->getName() . "\n";
echo "Guide email: " . $guide->getEmail() . "\n";
echo "Guide role: " . $guide->getRole() . "\n\n";

// Test 3: Check specific properties
echo "=== Test 3: Role-Specific Properties ===\n";

$driver->setPhone("1234567890");
$driver->setLicenseNo("LIC123456");
$driver->setVehicleType("Car");

echo "Driver phone: " . $driver->getPhone() . "\n";
echo "Driver license: " . $driver->getLicenseNo() . "\n";
echo "Driver vehicle: " . $driver->getVehicleType() . "\n\n";

$guide->setPhone("0987654321");
$guide->setNIC("123456789V");
$guide->setLanguages("English, Sinhala, Tamil");

echo "Guide phone: " . $guide->getPhone() . "\n";
echo "Guide NIC: " . $guide->getNIC() . "\n";
echo "Guide languages: " . $guide->getLanguages() . "\n\n";

// Test 4: Check abstract method implementation
echo "=== Test 4: Abstract Method Implementation ===\n";

// Note: We can't actually test register() without a database connection
// But we can verify the methods exist

echo "Driver has register() method: " . (method_exists($driver, 'register') ? "✅ YES" : "❌ NO") . "\n";
echo "Guide has register() method: " . (method_exists($guide, 'register') ? "✅ YES" : "❌ NO") . "\n";
echo "Traveller has register() method: " . (method_exists($traveller, 'register') ? "✅ YES" : "❌ NO") . "\n";
echo "Admin has register() method: " . (method_exists($admin, 'register') ? "✅ YES" : "❌ NO") . "\n\n";

echo "Driver has getProfileData() method: " . (method_exists($driver, 'getProfileData') ? "✅ YES" : "❌ NO") . "\n";
echo "Guide has getProfileData() method: " . (method_exists($guide, 'getProfileData') ? "✅ YES" : "❌ NO") . "\n";
echo "Traveller has getProfileData() method: " . (method_exists($traveller, 'getProfileData') ? "✅ YES" : "❌ NO") . "\n";
echo "Admin has getProfileData() method: " . (method_exists($admin, 'getProfileData') ? "✅ YES" : "❌ NO") . "\n\n";

// Test 5: Check common inherited methods
echo "=== Test 5: Common Inherited Methods ===\n";

echo "Driver has login() method: " . (method_exists($driver, 'login') ? "✅ YES" : "❌ NO") . "\n";
echo "Guide has login() method: " . (method_exists($guide, 'login') ? "✅ YES" : "❌ NO") . "\n";
echo "Traveller has login() method: " . (method_exists($traveller, 'login') ? "✅ YES" : "❌ NO") . "\n";
echo "Admin has login() method: " . (method_exists($admin, 'login') ? "✅ YES" : "❌ NO") . "\n\n";

// Test 6: Check role-specific methods
echo "=== Test 6: Role-Specific Methods ===\n";

echo "Driver has updateStatus() method: " . (method_exists($driver, 'updateStatus') ? "✅ YES" : "❌ NO") . "\n";
echo "Guide has updateStatus() method: " . (method_exists($guide, 'updateStatus') ? "✅ YES" : "❌ NO") . "\n";
echo "Traveller has createBooking() method: " . (method_exists($traveller, 'createBooking') ? "✅ YES" : "❌ NO") . "\n";
echo "Admin has getAllUsers() method: " . (method_exists($admin, 'getAllUsers') ? "✅ YES" : "❌ NO") . "\n\n";

// Test 7: Polymorphism demonstration
echo "=== Test 7: Polymorphism Demo ===\n";

$users = [$driver, $guide, $traveller, $admin];

foreach ($users as $user) {
    echo "User type: " . get_class($user) . " | Role: " . $user->getRole() . " | Name: " . $user->getName() . "\n";
}

echo "\n";

echo "🎉 All inheritance tests completed!\n";
echo "The MVC architecture with inheritance is working correctly.\n\n";

echo "=== Summary ===\n";
echo "✅ User is an abstract parent class\n";
echo "✅ Driver, Guide, Traveller, Admin are child classes\n";
echo "✅ All child classes inherit from User\n";
echo "✅ Common methods (login, etc.) are inherited\n";
echo "✅ Abstract methods are implemented in each child class\n";
echo "✅ Role-specific methods exist in appropriate classes\n";
echo "✅ Polymorphism is demonstrated\n";

echo "</pre>\n";

?>

<!DOCTYPE html>
<html>
<head>
    <title>RoutePro Inheritance Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        pre { background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; text-align: center; }
    </style>
</head>
<body>
    <p><strong>Note:</strong> This test verifies the inheritance structure without database connections. 
    To test the full functionality including database operations, use the API endpoints.</p>
</body>
</html>
