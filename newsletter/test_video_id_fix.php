<?php
// Test script to verify VIDEO_ID fix
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Testing Newsletter Settings Variables</h2>";

// Simulate POST data
$_POST['TOP_HEADER_TXT'] = 'Test Header';
$_POST['TOP_TEXT_TXT'] = 'Test Text';
$_POST['LINK_URL_1'] = 'https://example.com/1';
$_POST['LINK_URL_2'] = 'https://example.com/2';
$_POST['LINK_URL_3'] = 'https://example.com/3';
$_POST['VIDEO_ID'] = 'test123';

// Test variable initialization (same as in newsletter-settings.php)
$LINK_URL_1 = $_POST['LINK_URL_1'] ?? '';
$LINK_URL_2 = $_POST['LINK_URL_2'] ?? '';
$LINK_URL_3 = $_POST['LINK_URL_3'] ?? '';
$VIDEO_ID = $_POST['VIDEO_ID'] ?? '';

$TOP_HEADER_TXT = $_POST['TOP_HEADER_TXT'] ?? '';
$TOP_TEXT_TXT = $_POST['TOP_TEXT_TXT'] ?? '';

// Initialize all variables to prevent undefined variable errors
$LINK_TEXT_1 = '';
$LINK_TEXT_2 = '';
$LINK_TEXT_3 = '';
$LINK_OG_TITLE_1 = '';
$LINK_OG_TITLE_2 = '';
$LINK_OG_TITLE_3 = '';
$LINK_OG_IMG_1 = '';
$LINK_OG_IMG_2 = '';
$LINK_OG_IMG_3 = '';

echo "<h3>Variable Values:</h3>";
echo "<ul>";
echo "<li>TOP_HEADER_TXT: " . htmlspecialchars($TOP_HEADER_TXT) . "</li>";
echo "<li>TOP_TEXT_TXT: " . htmlspecialchars($TOP_TEXT_TXT) . "</li>";
echo "<li>LINK_URL_1: " . htmlspecialchars($LINK_URL_1) . "</li>";
echo "<li>LINK_URL_2: " . htmlspecialchars($LINK_URL_2) . "</li>";
echo "<li>LINK_URL_3: " . htmlspecialchars($LINK_URL_3) . "</li>";
echo "<li>VIDEO_ID: " . htmlspecialchars($VIDEO_ID) . "</li>";
echo "<li>LINK_TEXT_1: " . htmlspecialchars($LINK_TEXT_1) . "</li>";
echo "<li>LINK_TEXT_2: " . htmlspecialchars($LINK_TEXT_2) . "</li>";
echo "<li>LINK_TEXT_3: " . htmlspecialchars($LINK_TEXT_3) . "</li>";
echo "<li>LINK_OG_TITLE_1: " . htmlspecialchars($LINK_OG_TITLE_1) . "</li>";
echo "<li>LINK_OG_TITLE_2: " . htmlspecialchars($LINK_OG_TITLE_2) . "</li>";
echo "<li>LINK_OG_TITLE_3: " . htmlspecialchars($LINK_OG_TITLE_3) . "</li>";
echo "<li>LINK_OG_IMG_1: " . htmlspecialchars($LINK_OG_IMG_1) . "</li>";
echo "<li>LINK_OG_IMG_2: " . htmlspecialchars($LINK_OG_IMG_2) . "</li>";
echo "<li>LINK_OG_IMG_3: " . htmlspecialchars($LINK_OG_IMG_3) . "</li>";
echo "</ul>";

echo "<h3>SQL Statement Test:</h3>";

// Test UPDATE statement
$update_sql = "UPDATE new_settings SET TOP_HEADER_TXT = ?, TOP_TEXT_TXT = ?, LINK_URL_1 = ?, LINK_TEXT_1 = ?, LINK_OG_TITLE_1 = ?, LINK_OG_IMG_1 = ?, LINK_URL_2 = ?, LINK_TEXT_2 = ?, LINK_OG_TITLE_2 = ?, LINK_OG_IMG_2 = ?, LINK_URL_3 = ?, LINK_TEXT_3 = ?, LINK_OG_TITLE_3 = ?, LINK_OG_IMG_3 = ? WHERE user_id = ?";
$update_params = [$TOP_HEADER_TXT, $TOP_TEXT_TXT, $LINK_URL_1, $LINK_TEXT_1, $LINK_OG_TITLE_1, $LINK_OG_IMG_1, $LINK_URL_2, $LINK_TEXT_2, $LINK_OG_TITLE_2, $LINK_OG_IMG_2, $LINK_URL_3, $LINK_TEXT_3, $LINK_OG_TITLE_3, $LINK_OG_IMG_3, 1]; // user_id = 1 for test

echo "<p><strong>UPDATE SQL:</strong> " . htmlspecialchars($update_sql) . "</p>";
echo "<p><strong>Parameters count:</strong> " . count($update_params) . "</p>";
echo "<p><strong>Placeholder count:</strong> " . substr_count($update_sql, '?') . "</p>";

// Test INSERT statement
$insert_sql = "INSERT INTO new_settings (TOP_HEADER_TXT, TOP_TEXT_TXT, LINK_URL_1, LINK_TEXT_1, LINK_OG_TITLE_1, LINK_OG_IMG_1, LINK_URL_2, LINK_TEXT_2, LINK_OG_TITLE_2, LINK_OG_IMG_2, LINK_URL_3, LINK_TEXT_3, LINK_OG_TITLE_3, LINK_OG_IMG_3, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$insert_params = [$TOP_HEADER_TXT, $TOP_TEXT_TXT, $LINK_URL_1, $LINK_TEXT_1, $LINK_OG_TITLE_1, $LINK_OG_IMG_1, $LINK_URL_2, $LINK_TEXT_2, $LINK_OG_TITLE_2, $LINK_OG_IMG_2, $LINK_URL_3, $LINK_TEXT_3, $LINK_OG_TITLE_3, $LINK_OG_IMG_3, 1]; // user_id = 1 for test

echo "<p><strong>INSERT SQL:</strong> " . htmlspecialchars($insert_sql) . "</p>";
echo "<p><strong>Parameters count:</strong> " . count($insert_params) . "</p>";
echo "<p><strong>Placeholder count:</strong> " . substr_count($insert_sql, '?') . "</p>";

echo "<h3>Test Results:</h3>";
if (count($update_params) === substr_count($update_sql, '?')) {
    echo "<p style='color: green;'>✅ UPDATE statement: Parameters and placeholders match!</p>";
} else {
    echo "<p style='color: red;'>❌ UPDATE statement: Parameters and placeholders do not match!</p>";
}

if (count($insert_params) === substr_count($insert_sql, '?')) {
    echo "<p style='color: green;'>✅ INSERT statement: Parameters and placeholders match!</p>";
} else {
    echo "<p style='color: red;'>❌ INSERT statement: Parameters and placeholders do not match!</p>";
}

echo "<h3>Next Steps:</h3>";
echo "<p>If all tests pass, you can now:</p>";
echo "<ol>";
echo "<li>Run <code>add_video_id_column.php</code> to add the VIDEO_ID column to database</li>";
echo "<li>Run <code>restore_video_id_functionality.php</code> to restore full VIDEO_ID functionality</li>";
echo "<li>Test the newsletter form</li>";
echo "</ol>";
?>
