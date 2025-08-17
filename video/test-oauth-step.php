<?php
/**
 * Test OAuth2 step by step to identify errors
 */
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>OAuth2 Test - Step by Step</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .step { margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; }
    </style>
</head>
<body>
    <h1>OAuth2 Test - Step by Step</h1>
    
    <?php
    // Step 1: Check if session works
    echo '<div class="step info">';
    echo '<h3>Step 1: Session Check</h3>';
    session_start();
    echo 'Session started: ' . (session_status() === PHP_SESSION_ACTIVE ? 'SUCCESS' : 'FAILED') . '<br>';
    echo 'Session ID: ' . session_id() . '<br>';
    echo '</div>';
    
    // Step 2: Check if oauth-config.php exists
    echo '<div class="step info">';
    echo '<h3>Step 2: OAuth Config File Check</h3>';
    $oauthConfigPath = __DIR__ . '/oauth-config.php';
    echo 'OAuth config path: ' . $oauthConfigPath . '<br>';
    echo 'File exists: ' . (file_exists($oauthConfigPath) ? 'YES' : 'NO') . '<br>';
    echo '</div>';
    
    // Step 3: Check if credentials.php exists
    echo '<div class="step info">';
    echo '<h3>Step 3: Credentials File Check</h3>';
    $credentialsPath = __DIR__ . '/../credentials.php';
    echo 'Credentials path: ' . $credentialsPath . '<br>';
    echo 'File exists: ' . (file_exists($credentialsPath) ? 'YES' : 'NO') . '<br>';
    echo '</div>';
    
    // Step 4: Try to include oauth-config.php
    echo '<div class="step info">';
    echo '<h3>Step 4: Include OAuth Config</h3>';
    try {
        if (file_exists($oauthConfigPath)) {
            require_once $oauthConfigPath;
            echo 'OAuth config included: SUCCESS<br>';
            
            // Check if functions exist
            echo 'getYouTubeAuthUrl function exists: ' . (function_exists('getYouTubeAuthUrl') ? 'YES' : 'NO') . '<br>';
            echo 'hasValidYouTubeTokens function exists: ' . (function_exists('hasValidYouTubeTokens') ? 'YES' : 'NO') . '<br>';
            echo 'getYouTubeAccessToken function exists: ' . (function_exists('getYouTubeAccessToken') ? 'YES' : 'NO') . '<br>';
            
            // Check if constants are defined
            echo 'YOUTUBE_CLIENT_ID defined: ' . (defined('YOUTUBE_CLIENT_ID') ? 'YES' : 'NO') . '<br>';
            echo 'YOUTUBE_CLIENT_SECRET defined: ' . (defined('YOUTUBE_CLIENT_SECRET') ? 'YES' : 'NO') . '<br>';
            echo 'YOUTUBE_REDIRECT_URI defined: ' . (defined('YOUTUBE_REDIRECT_URI') ? 'YES' : 'NO') . '<br>';
            
        } else {
            echo 'OAuth config file not found<br>';
        }
    } catch (Exception $e) {
        echo 'Error including oauth-config.php: ' . $e->getMessage() . '<br>';
    }
    echo '</div>';
    
    // Step 5: Check session variables
    echo '<div class="step info">';
    echo '<h3>Step 5: Session Variables Check</h3>';
    echo 'youtube_access_token: ' . (isset($_SESSION['youtube_access_token']) ? 'SET' : 'NOT SET') . '<br>';
    echo 'youtube_refresh_token: ' . (isset($_SESSION['youtube_refresh_token']) ? 'SET' : 'NOT SET') . '<br>';
    echo 'youtube_token_expires: ' . (isset($_SESSION['youtube_token_expires']) ? 'SET' : 'NOT SET') . '<br>';
    echo '</div>';
    
    // Step 6: Test OAuth functions if they exist
    if (function_exists('hasValidYouTubeTokens') && function_exists('getYouTubeAuthUrl')) {
        echo '<div class="step info">';
        echo '<h3>Step 6: OAuth Functions Test</h3>';
        
        $hasTokens = hasValidYouTubeTokens();
        echo 'hasValidYouTubeTokens(): ' . ($hasTokens ? 'TRUE' : 'FALSE') . '<br>';
        
        if (!$hasTokens) {
            $authUrl = getYouTubeAuthUrl();
            echo 'OAuth URL generated: ' . (strlen($authUrl) > 0 ? 'YES' : 'NO') . '<br>';
            echo 'Auth URL: ' . htmlspecialchars($authUrl) . '<br>';
        }
        
        echo '</div>';
    }
    ?>
    
    <div class="step">
        <h3>Next Steps:</h3>
        <p>If you see any errors above, they need to be fixed before the main transcript functionality will work.</p>
        <p>Check the server error logs for more detailed information.</p>
    </div>
</body>
</html>
