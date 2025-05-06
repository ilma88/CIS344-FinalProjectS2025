<?php
include "PharmacyDatabase.php";
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$db = new PharmacyDatabase();
$userId = $_GET["userId"] ?? 1; // You may want to adjust this
$prescriptions = $db->getPrescriptionsByUser($userId);

echo "<h2>Prescriptions for User ID: $userId</h2><ul>";
foreach ($prescriptions as $p) {
    echo "<li><strong>Prescription ID:</strong> {$p['prescriptionId']}, Medication ID: {$p['medicationId']}, Quantity: {$p['quantity']}, Instructions: {$p['dosageInstructions']}</li>";
}
echo "</ul>";
?>