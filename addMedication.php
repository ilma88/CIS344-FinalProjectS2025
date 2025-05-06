<?php
include "PharmacyDatabase.php";
$db = new PharmacyDatabase();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $dosage = $_POST["dosage"];
    $manufacturer = $_POST["manufacturer"];
    $db->addMedication($name, $dosage, $manufacturer);
    echo "Medication added.<br><br>";
}
?>

<form method="POST">
    <h2>Add Medication</h2>
    Name: <input type="text" name="name"><br>
    Dosage: <input type="text" name="dosage"><br>
    Manufacturer: <input type="text" name="manufacturer"><br><br>
    <input type="submit" value="Add Medication">
</form>