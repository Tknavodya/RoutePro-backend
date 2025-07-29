<?php
class Guide {
    private $id;
    private $user_id;
    private $name;
    private $phone;
    private $status;
    private $nic;
    private $license_no;
    private $experience;
    private $location;
    private $languages;

    public function __construct(
        $user_id = null,
        $name = null,
        $phone = null,
        $nic = null,
        $license_no = null,
        $experience = null,
        $location = null,
        $languages = null,
        $status = 'nonavailable'
    ) {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->phone = $phone;
        $this->nic = $nic;
        $this->license_no = $license_no;
        $this->experience = $experience;
        $this->location = $location;
        $this->languages = $languages;
        $this->status = $status;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getName() { return $this->name; }
    public function getPhone() { return $this->phone; }
    public function getNIC() { return $this->nic; }
    public function getLicenseNo() { return $this->license_no; }
    public function getExperience() { return $this->experience; }
    public function getLocation() { return $this->location; }
    public function getLanguages() { return $this->languages; }
    public function getStatus() { return $this->status; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setName($name) { $this->name = $name; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setNIC($nic) { $this->nic = $nic; }
    public function setLicenseNo($license_no) { $this->license_no = $license_no; }
    public function setExperience($experience) { $this->experience = $experience; }
    public function setLocation($location) { $this->location = $location; }
    public function setLanguages($languages) { $this->languages = $languages; }
    public function setStatus($status) { $this->status = $status; }

    // Save guide to database
    public function save($connection) {
        try {
            $sql = "INSERT INTO guides (user_id, name, phone, nic, license_no, experience, location, languages, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $connection->prepare($sql);
            return $stmt->execute([
                $this->user_id,
                $this->name,
                $this->phone,
                $this->nic,
                $this->license_no,
                $this->experience,
                $this->location,
                $this->languages,
                $this->status
            ]);
        } catch (PDOException $e) {
            error_log("Guide Save Error: " . $e->getMessage());
            return false;
        }
    }

    // (Optional) Fetch guide by user ID
    public static function getByUserId($connection, $user_id) {
        try {
            $sql = "SELECT * FROM guides WHERE user_id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Guide Fetch Error: " . $e->getMessage());
            return null;
        }
    }
}
?>
