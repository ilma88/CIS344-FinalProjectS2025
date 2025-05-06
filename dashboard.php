<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}
echo "<h2>Welcome, " . $_SESSION["username"] . " (" . $_SESSION["userType"] . ")</h2>";
echo "<a href='logout.php'>Logout</a><br><br>";

if ($_SESSION["userType"] == "pharmacist") {
    echo "<a href='addMedication.php'>Add Medication</a><br>";
    echo "<a href='addPrescription.php'>Add Prescription</a><br>";
} else {
    echo "<a href='viewPrescriptions.php'>View Prescriptions</a><br>";
}
?>