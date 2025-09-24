<?php
// Database connection file - secure and error-handled
$servername = "localhost"; // Adjust if your host is different (e.g., remote MySQL)
$username = "uhpdlnsnj1voi";
$password = "rowrmxvbu3z5";
$dbname = "dbjxdnymwjk4ws";
 
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
 
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
// Set charset to UTF-8 for international support
$conn->set_charset("utf8mb4");
?>
