<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "final_project_db"; // Make sure this matches your phpMyAdmin DB name

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

