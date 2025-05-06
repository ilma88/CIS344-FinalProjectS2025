<?php
class PharmacyDatabase {
    private $conn;

    public function __construct() {
        $host = "localhost";
        $db = "pharmacy_portal_db";
        $user = "root";
        $pass = ""; // Set your MySQL password here

        $this->conn = new mysqli($host, $user, $pass, $db);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function addUser($userName, $contactInfo, $userType) {
        $stmt = $this->conn->prepare("CALL AddOrUpdateUser(?, ?, ?)");
        $stmt->bind_param("sss", $userName, $contactInfo, $userType);
        $stmt->execute();
        $stmt->close();
    }

    public function getUserDetails($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM Users WHERE userId = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result;
    }

    public function addMedication($medicationName, $dosage, $manufacturer) {
        $stmt = $this->conn->prepare("INSERT INTO Medications (medicationName, dosage, manufacturer) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $medicationName, $dosage, $manufacturer);
        $stmt->execute();
        $stmt->close();
    }

    public function addPrescription($userId, $medicationId, $dosageInstructions, $quantity) {
        $stmt = $this->conn->prepare("INSERT INTO Prescriptions (userId, medicationId, prescribedDate, dosageInstructions, quantity) VALUES (?, ?, NOW(), ?, ?)");
        $stmt->bind_param("iisi", $userId, $medicationId, $dosageInstructions, $quantity);
        $stmt->execute();
        $stmt->close();
    }

    public function getPrescriptionsByUser($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM Prescriptions WHERE userId = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $prescriptions = [];
        while ($row = $result->fetch_assoc()) {
            $prescriptions[] = $row;
        }
        $stmt->close();
        return $prescriptions;
    }

    public function getMedicationInventory() {
        $result = $this->conn->query("SELECT * FROM MedicationInventoryView");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>