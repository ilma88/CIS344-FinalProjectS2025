<?php
include "PharmacyDatabase.php";
$db = new PharmacyDatabase();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST["userId"];
    $medicationId = $_POST["medicationId"];
    $instructions = $_POST["instructions"];
    $quantity = $_POST["quantity"];
    $db->addPrescription($userId, $medicationId, $instructions, $quantity);
    echo "Prescription added.<br><br>";
}
?>

<form method="POST">
    <h2>Add Prescription</h2>
    Patient User ID: <input type="number" name="userId"><br>
    Medication ID: <input type="number" name="medicationId"><br>
    Instructions: <input type="text" name="instructions"><br>
    Quantity: <input type="number" name="quantity"><br><br>
    <input type="submit" value="Add Prescription">
</form>
