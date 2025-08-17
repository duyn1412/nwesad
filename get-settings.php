<?php
// Include your database connection file
include 'connect-sql.php';

// Prepare an SQL statement to fetch the settings
$stmt = $conn->prepare("SELECT openai_api_key, newsletter_assistant_id, blog_assistant_id, video_assistant_id, html FROM settings ORDER BY id DESC LIMIT 1");

// Execute the statement
$stmt->execute();

// Bind the result variables
$stmt->bind_result($openai_api_key, $newsletter_assistant_id, $blog_assistant_id, $video_assistant_id, $html);

// Fetch the data
$stmt->fetch();

// Close the statement
$stmt->close();

// Close the database connection
//$conn->close();
?>