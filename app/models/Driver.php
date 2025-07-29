<?php
class Driver {
    private $id;
    private $user_id;
    private $name;
    private $phone;
    private $status;
    private $license_no;
    private $vehicle_type;
    private $experience;
    private $location;

    public function __construct(
        $user_id = null,
        $name = null,
        $phone = null,
        $license_no = null,
        $vehicle_type = null,
        $experience = null,
        $location = null,
        $status = 'nonavailable'
    ) {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->phone = $phone;
        $this->license_no = $license_no;
        $this->vehicle_type = $vehicle_type;
        $this->experience = $experience;
        $this->location = $location;
        $this->status = $status;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getName() { return $this->name; }
    public function getPhone() { return $this->phone; }
    public function getStatus() { return $this->status; }
    public function getLicenseNo() { return $this->license_no; }
    public function getVehicleType() { return $this->vehicle_type; }
    public function getExperience() { return $this->experience; }
    public function getLocation() { return $this->location; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setName($name) { $this->name = $name; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setStatus($status) { $this->status = $status; }
    public function setLicenseNo($license_no) { $this->license_no = $license_no; }
    public function setVehicleType($vehicle_type) { $this->vehicle_type = $vehicle_type; }
    public function setExperience($experience) { $this->experience = $experience; }
    public function setLocation($location) { $this->location = $location; }

    // Save driver to database
    public function save($connection) {
        try {
            $sql = "INSERT INTO drivers (user_id, name, phone, license_no, vehicle_type, experience, location, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $connection->prepare($sql);
            return $stmt->execute([
                $this->user_id,
                $this->name,
                $this->phone,
                $this->license_no,
                $this->vehicle_type,
                $this->experience,
                $this->location,
                $this->status
            ]);
        } catch (PDOException $e) {
            error_log("Driver Save Error: " . $e->getMessage());
            return false;
        }
    }

    // (Optional) Fetch by user ID
    public static function getByUserId($connection, $user_id) {
        try {
            $sql = "SELECT * FROM drivers WHERE user_id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Driver Fetch Error: " . $e->getMessage());
            return null;
        }
    }
}
