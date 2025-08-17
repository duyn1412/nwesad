<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// Start the session
session_start();

// Include your database connection file
include 'connect-sql.php';

// Get the form data
$openai_api_key = $_POST['openai_api_key'];
$newsletter_assistant_id = $_POST['newsletter_assistant_id'];
$blog_assistant_id = $_POST['blog_assistant_id'];
$video_assistant_id = $_POST['video_assistant_id'];
$html = htmlspecialchars($_POST['html']); // Get the html field

// Prepare an SQL statement to check if a row already exists
$stmt = $conn->prepare("SELECT * FROM settings");
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // A row already exists, so update it
    $stmt = $conn->prepare("UPDATE settings SET openai_api_key = ?, newsletter_assistant_id = ?, blog_assistant_id = ?, video_assistant_id = ?, html = ?");
} else {
    // No row exists, so insert a new one
    $stmt = $conn->prepare("INSERT INTO settings (openai_api_key, newsletter_assistant_id, blog_assistant_id, video_assistant_id, html) VALUES (?, ?, ?, ?, ?)");
}

// Bind parameters
$stmt->bind_param("sssss", $openai_api_key, $newsletter_assistant_id, $blog_assistant_id, $video_assistant_id, $html);

// Execute the statement
if ($stmt->execute()) {
    // Set a session variable with the success message
    $_SESSION['message'] = "Settings saved successfully.";
} else {
    // Set a session variable with the error message
    $_SESSION['message'] = "Error: " . $stmt->error;
}

// Redirect to the current page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;

// Close the statement
$stmt->close();

// Close the database connection
$conn->close();
?>