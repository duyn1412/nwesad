<?php
// Database connection parameters
$servername = "localhost";
$username = "nwengine_dev";
$password = "Jy#S._UJ%(95";
$database = "nwengine_pcblaminates";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset('utf8mb4');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>