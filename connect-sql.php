<?php
// Database connection - Direct credentials for testing
$servername = "localhost";
$username = "nwengine_dev";
$password = "Jy#S._UJ%(95";
$database = "nwengine_pcblaminates";

// Create connection
try {
    $conn = new mysqli($servername, $username, $password, $database);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// Test connection
if ($conn->ping()) {
    // Connection is working
} else {
    die("Database connection lost");
}
?>