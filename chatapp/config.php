<?php
$servername = "sql109.infinityfree.com";
$username = "if0_36951144"; // Default username for XAMPP MySQL
$password = "PfbRqzCyEmtITR0"; // Default password for XAMPP MySQL (usually empty)
$dbname = "if0_36951144_chatapp_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
