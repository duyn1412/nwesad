<?php
// Database connection - credentials loaded from separate file
if (file_exists(__DIR__ . '/credentials.php')) {
    require_once __DIR__ . '/credentials.php';
    
    // Use credentials from file if available
    if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS')) {
        $servername = DB_HOST;
        $username = DB_USER;
        $password = DB_PASS;
        $database = DB_NAME;
    } else {
        // Fallback to hardcoded values (for backward compatibility)
        $servername = "localhost";
        $username = "nwengine_dev";
        $password = "Jy#S._UJ%(95";
        $database = "nwengine_pcblaminates";
    }
} else {
    // Fallback to hardcoded values if credentials file doesn't exist
    $servername = "localhost";
    $username = "nwengine_dev";
    $password = "Jy#S._UJ%(95";
    $database = "nwengine_pcblaminates";
}

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