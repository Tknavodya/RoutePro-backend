# RoutePro API Documentation

## Base URL
```
http://localhost/RoutePro-backend(02)/public/
```

## Authentication
The API uses session-based authentication. After login, the session will be maintained for subsequent requests.

---

## 🔐 Authentication Endpoints

### Login
**POST** `/auth/login`

Login for any user type (Driver, Guide, Traveller, Admin).

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password123",
    "role": "driver" // Optional: driver, guide, traveller, admin
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "driver",
        "rating": 4.5,
        "profile": {
            // Role-specific profile data
        }
    }
}
```

### Register
**POST** `/auth/register`

Register a new user. Fields required depend on the role.

**Driver Registration:**
```json
{
    "role": "driver",
    "name": "John Doe",
    "email": "john@example.com",
    "password": "securepassword",
    "phone": "1234567890",
    "license_no": "LIC123456",
    "vehicle_type": "Car",
    "experience": "5 years",
    "location": "Colombo"
}
```

**Guide Registration:**
```json
{
    "role": "guide",
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "securepassword",
    "phone": "0987654321",
    "nic": "123456789V",
    "license_no": "GLIC123456",
    "experience": "3 years",
    "location": "Kandy",
    "languages": "English, Sinhala, Tamil"
}
```

**Traveller Registration:**
```json
{
    "role": "traveller",
    "name": "Bob Wilson",
    "email": "bob@example.com",
    "password": "securepassword",
    "phone": "1122334455"
}
```

**Admin Registration:**
```json
{
    "role": "admin",
    "name": "Alice Admin",
    "email": "alice@example.com",
    "password": "securepassword",
    "department": "IT",
    "permissions": "full"
}
```

### Logout
**POST** `/auth/logout`

Logout current user and destroy session.

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

---

## 🚗 Driver Endpoints

### Get Driver Profile
**GET** `/driver/profile`
*Requires: Driver or Admin role*

**Response:**
```json
{
    "success": true,
    "profile": {
        "id": 1,
        "user_id": 1,
        "name": "John Doe",
        "phone": "1234567890",
        "license_no": "LIC123456",
        "vehicle_type": "Car",
        "experience": "5 years",
        "location": "Colombo",
        "status": "available",
        "email": "john@example.com",
        "rating": 4.5
    }
}
```

### Update Driver Profile
**PUT** `/driver/profile`
*Requires: Driver or Admin role*

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "phone": "1234567890",
    "license_no": "LIC123456",
    "vehicle_type": "SUV",
    "experience": "6 years",
    "location": "Gampaha"
}
```

### Update Driver Status
**PUT** `/driver/status`
*Requires: Driver or Admin role*

**Request Body:**
```json
{
    "status": "available" // available, nonavailable, busy
}
```

### Update Driver Location
**PUT** `/driver/location`
*Requires: Driver or Admin role*

**Request Body:**
```json
{
    "location": "New Location"
}
```

### Get Available Drivers
**GET** `/drivers/available`

**Response:**
```json
{
    "success": true,
    "drivers": [
        {
            "id": 1,
            "name": "John Doe",
            "vehicle_type": "Car",
            "location": "Colombo",
            "rating": 4.5,
            "experience": "5 years"
        }
    ]
}
```

---

## 🗺️ Guide Endpoints

### Get Guide Profile
**GET** `/guide/profile`
*Requires: Guide or Admin role*

### Update Guide Profile
**PUT** `/guide/profile`
*Requires: Guide or Admin role*

**Request Body:**
```json
{
    "name": "Jane Smith",
    "phone": "0987654321",
    "nic": "123456789V",
    "license_no": "GLIC123456",
    "experience": "4 years",
    "location": "Kandy",
    "languages": "English, Sinhala, Tamil, German"
}
```

### Update Guide Status
**PUT** `/guide/status`
*Requires: Guide or Admin role*

**Request Body:**
```json
{
    "status": "available"
}
```

### Update Guide Location
**PUT** `/guide/location`
*Requires: Guide or Admin role*

**Request Body:**
```json
{
    "location": "Nuwara Eliya"
}
```

### Get Available Guides
**GET** `/guides/available`

### Get Guides by Language
**POST** `/guides/by-language`

**Request Body:**
```json
{
    "language": "English"
}
```

---

## 🧳 Traveller Endpoints

### Get Traveller Profile
**GET** `/traveller/profile`
*Requires: Traveller or Admin role*

### Update Traveller Profile
**PUT** `/traveller/profile`
*Requires: Traveller or Admin role*

**Request Body:**
```json
{
    "name": "Bob Wilson Updated",
    "phone": "1122334455"
}
```

### Get Booking History
**GET** `/traveller/bookings`
*Requires: Traveller or Admin role*

**Response:**
```json
{
    "success": true,
    "bookings": [
        {
            "id": 1,
            "route_name": "Colombo to Kandy",
            "start_location": "Colombo",
            "end_location": "Kandy",
            "status": "completed",
            "booking_date": "2025-08-01",
            "total_price": 5000.00
        }
    ]
}
```

### Create Booking
**POST** `/traveller/bookings`
*Requires: Traveller or Admin role*

**Request Body:**
```json
{
    "route_id": 1,
    "driver_id": 2, // Optional
    "guide_id": 3   // Optional
}
```

---

## 👑 Admin Endpoints

### Get All Users
**GET** `/admin/users`
*Requires: Admin role*

**Response:**
```json
{
    "success": true,
    "users": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "driver",
            "rating": 4.5,
            "created_at": "2025-08-01 10:00:00"
        }
    ]
}
```

### Get Users by Role
**POST** `/admin/users/by-role`
*Requires: Admin role*

**Request Body:**
```json
{
    "role": "driver"
}
```

### Delete User
**DELETE** `/admin/users/delete`
*Requires: Admin role*

**Request Body:**
```json
{
    "user_id": 5
}
```

### Update User Role
**PUT** `/admin/users/update-role`
*Requires: Admin role*

**Request Body:**
```json
{
    "user_id": 5,
    "new_role": "guide"
}
```

### Get System Statistics
**GET** `/admin/stats`
*Requires: Admin role*

**Response:**
```json
{
    "success": true,
    "stats": {
        "user_counts": [
            {"role": "driver", "count": 15},
            {"role": "guide", "count": 8},
            {"role": "traveller", "count": 45},
            {"role": "admin", "count": 2}
        ],
        "total_bookings": 123,
        "active_drivers": 8,
        "active_guides": 5
    }
}
```

---

## 🔗 Legacy Endpoints

For backward compatibility, the following endpoints are still available:

### Legacy Login
**POST** `/app/controllers/Login.php`

This endpoint now uses the new inheritance structure internally but maintains the same interface.

---

## 📝 Response Format

All API responses follow this format:

**Success Response:**
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {
        // Response data
    }
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        // Validation errors (if applicable)
    }
}
```

---

## 🔒 HTTP Status Codes

- `200` - Success
- `201` - Created (for registration)
- `400` - Bad Request (validation errors)
- `401` - Unauthorized (authentication required)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found
- `405` - Method Not Allowed
- `500` - Internal Server Error

---

## 🧪 Testing

1. **Setup Database**: Run `setup_database.php` first
2. **Test Inheritance**: Visit `test_inheritance.php` to verify the class structure
3. **Test API**: Use Postman or any API client to test endpoints
4. **Sample Admin**: Use `admin@routepro.com` / `admin123` for admin testing

---

## 🏗️ Architecture Notes

This API demonstrates **Object-Oriented Programming** with **Inheritance**:

- **User** (Abstract Parent Class) - Contains common properties and methods
- **Driver, Guide, Traveller, Admin** (Child Classes) - Inherit from User and add specific functionality
- **MVC Pattern** - Clear separation of Models, Views (JSON responses), and Controllers
- **Polymorphism** - Same method names behave differently for each user type
- **Code Reusability** - Common functionality shared through inheritance
