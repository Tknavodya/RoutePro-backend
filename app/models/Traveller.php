<?php
class Traveller {
    private $id;
    private $user_id;
    private $name;
    private $phone;

    public function __construct($user_id = null, $name = null, $phone = null) {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->phone = $phone;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getName() { return $this->name; }
    public function getPhone() { return $this->phone; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setName($name) { $this->name = $name; }
    public function setPhone($phone) { $this->phone = $phone; }

    // Save traveler to database
    public function save($connection) {
        try {
            $sql = "INSERT INTO travellers (user_id, name, phone) VALUES (?, ?, ?)";
            $stmt = $connection->prepare($sql);
            return $stmt->execute([
                $this->user_id,
                $this->name,
                $this->phone
            ]);
        } catch (PDOException $e) {
            error_log("Traveller Save Error: " . $e->getMessage());
            return false;
        }
    }

    // (Optional) Fetch traveler by user ID
    public static function getByUserId($connection, $user_id) {
        try {
            $sql = "SELECT * FROM travellers WHERE user_id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Traveller Fetch Error: " . $e->getMessage());
            return null;
        }
    }
}
