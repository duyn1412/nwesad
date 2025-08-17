<?php
// Include your database connection file
include '../connect-sql.php';

// Assume that $username is already defined
// If not, you need to define it here
// $username = "your_username";
$username = $_COOKIE['username'];
// Prepare an SQL statement to get the user_id
$stmt = $conn->prepare("SELECT id FROM nwengine_user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close(); // Close the statement

// Prepare an SQL statement to fetch the settings
$stmt = $conn->prepare("SELECT TOP_HEADER_TXT, TOP_TEXT_TXT, LINK_URL_1, LINK_TEXT_1, LINK_OG_TITLE_1, LINK_OG_IMG_1, LINK_URL_2, LINK_TEXT_2, LINK_OG_TITLE_2, LINK_OG_IMG_2, LINK_URL_3, LINK_TEXT_3, LINK_OG_TITLE_3, LINK_OG_IMG_3, VIDEO_ID FROM new_settings WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);

// Execute the statement
$stmt->execute();

// Bind the result variables
$stmt->bind_result($TOP_HEADER_TXT, $TOP_TEXT_TXT, $LINK_URL_1, $LINK_TEXT_1, $LINK_OG_TITLE_1, $LINK_OG_IMG_1, $LINK_URL_2, $LINK_TEXT_2, $LINK_OG_TITLE_2, $LINK_OG_IMG_2, $LINK_URL_3, $LINK_TEXT_3, $LINK_OG_TITLE_3, $LINK_OG_IMG_3,$VIDEO_ID);

// Initialize VIDEO_ID variable (will be added after column is created)
// $VIDEO_ID = '';

// Fetch the data
$stmt->fetch();

// Close the statement
$stmt->close();

// Close the database connection
//$conn->close();
?>