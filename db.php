<?php
// db.php - central DB connection

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';                // Your MySQL root password (leave empty if none)
$DB_NAME = 'blood_donation';  // Your database name

// Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset('utf8mb4');
?>
