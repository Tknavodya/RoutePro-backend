<?php
// User.php - Updated to match your exact database structure
class User {
    private $id;
    private $name;
    private $email;
    private $password;
    private $role;
    private $rating;
    private $created_at;
    private $reset_token;
    private $reset_token_expiry;


    // Constructor
    public function __construct(
        $name = null,
        $email = null,
        $password = null,
        $role = null,
        $rating = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->rating = $rating;
    }


    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getRole() { return $this->role; }
    public function getRating() { return $this->rating; }
    public function getCreatedAt() { return $this->created_at; }
    public function getResetToken() { return $this->reset_token; }
    public function getResetTokenExpiry() { return $this->reset_token_expiry; }


    // Setters
    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = $password; }
    public function setRole($role) { $this->role = $role; }
    public function setRating($rating) { $this->rating = $rating; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }
    public function setResetToken($reset_token) { $this->reset_token = $reset_token; }
    public function setResetTokenExpiry($reset_token_expiry) { $this->reset_token_expiry = $reset_token_expiry; }


    public function login($connection) {
        try {
            $sql = "SELECT id, name, email, password, role, rating, created_at
                    FROM users
                    WHERE email = ?";
           
            $stmt = $connection->prepare($sql);
            $stmt->bindValue(1, $this->email);
            $stmt->execute();
           
            $row = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($row && password_verify($this->password, $row['password'])) {
                // Create user object with fetched data
                $user = new User();
                $user->setId($row['id']);
                $user->setName($row['name']);
                $user->setEmail($row['email']);
                $user->setRole($row['role']);
                $user->setRating($row['rating']);
                $user->setCreatedAt($row['created_at']);
               
                return $user;
            }


            return null;
        } catch (PDOException $e) {
            error_log("Login query error: " . $e->getMessage());
            return null;
        }
    }
}