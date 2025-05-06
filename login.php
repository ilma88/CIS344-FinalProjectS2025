<?php
session_start();
include "PharmacyDatabase.php";
$db = new PharmacyDatabase();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $userType = $_POST["userType"]; // 'pharmacist' or 'patient'
    $contact = $_POST["contactInfo"];

    $db->addUser($username, $contact, $userType);

    $_SESSION["username"] = $username;
    $_SESSION["userType"] = $userType;

    header("Location: dashboard.php");
    exit;
}
?>

<form method="POST">
    <h2>Login</h2>
    Username: <input type="text" name="username" required><br>
    Contact Info: <input type="text" name="contactInfo"><br>
    Role:
    <select name="userType">
        <option value="pharmacist">Pharmacist</option>
        <option value="patient">Patient</option>
    </select><br><br>
    <input type="submit" value="Login">
</form>
