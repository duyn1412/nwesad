<?php
/**
 * YouTube OAuth2 Configuration
 * This file reads credentials from a separate credentials.php file
 * 
 * IMPORTANT: Make sure credentials.php exists and contains the required constants
 */

// Include credentials file - look in root directory
$credentialsPath = __DIR__ . '/../credentials.php';
if (!file_exists($credentialsPath)) {
    // Try alternative paths
    $alternativePaths = [
        __DIR__ . '/credentials.php',  // Same directory
        dirname(__DIR__) . '/credentials.php',  // Root directory
        '/home/nwengine/public_html/nwesadmin/credentials.php'  // Absolute path
    ];
    
    $found = false;
    foreach ($alternativePaths as $path) {
        if (file_exists($path)) {
            $credentialsPath = $path;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        die('Error: credentials.php file not found. Please create this file with your OAuth credentials.');
    }
}

require_once $credentialsPath;

// Verify required credentials are defined
if (!defined('YOUTUBE_CLIENT_ID') || !defined('YOUTUBE_CLIENT_SECRET') || !defined('YOUTUBE_REDIRECT_URI')) {
    die('Error: Required OAuth credentials are not defined in credentials.php');
}

// YouTube API Scopes needed for transcript access
define('YOUTUBE_SCOPES', [
    'https://www.googleapis.com/auth/youtube.force-ssl',
    'https://www.googleapis.com/auth/youtube.readonly',
    'https://www.googleapis.com/auth/youtube',
    'https://www.googleapis.com/auth/youtubepartner'
]);

// OAuth2 Authorization URL
define('YOUTUBE_AUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth');

/**
 * Generate OAuth2 Authorization URL
 */
function getYouTubeAuthUrl() {
    $params = [
        'client_id' => YOUTUBE_CLIENT_ID,
        'redirect_uri' => YOUTUBE_REDIRECT_URI,
        'scope' => implode(' ', YOUTUBE_SCOPES),
        'response_type' => 'code',
        'access_type' => 'offline',
        'prompt' => 'consent'
    ];
    
    return YOUTUBE_AUTH_URL . '?' . http_build_query($params);
}

/**
 * Check if user has valid OAuth2 tokens
 */
function hasValidYouTubeTokens() {
    if (!isset($_SESSION['youtube_access_token'])) {
        return false;
    }
    
    // Check if token is expired
    if (isset($_SESSION['youtube_token_expires']) && time() > $_SESSION['youtube_token_expires']) {
        return false;
    }
    
    return true;
}

/**
 * Get current access token
 */
function getYouTubeAccessToken() {
    if (hasValidYouTubeTokens()) {
        return $_SESSION['youtube_access_token'];
    }
    
    // Try to refresh token if expired
    if (isset($_SESSION['youtube_refresh_token'])) {
        $newToken = refreshYouTubeToken($_SESSION['youtube_refresh_token']);
        if ($newToken) {
            return $newToken;
        }
    }
    
    return null;
}

/**
 * Refresh access token using refresh token
 */
function refreshYouTubeToken($refreshToken) {
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $postData = [
        'client_id' => YOUTUBE_CLIENT_ID,
        'client_secret' => YOUTUBE_CLIENT_SECRET,
        'refresh_token' => $refreshToken,
        'grant_type' => 'refresh_token'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $tokenData = json_decode($response, true);
        
        if (isset($tokenData['access_token'])) {
            // Update session with new token
            $_SESSION['youtube_access_token'] = $tokenData['access_token'];
            $_SESSION['youtube_token_expires'] = time() + $tokenData['expires_in'];
            
            return $tokenData['access_token'];
        }
    }
    
    return null;
}

/**
 * Make authenticated request to YouTube API
 */
function makeYouTubeApiRequest($url, $method = 'GET', $data = null) {
    $accessToken = getYouTubeAccessToken();
    
    if (!$accessToken) {
        return ['error' => 'No valid access token available'];
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    
    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        return json_decode($response, true);
    } else {
        return ['error' => 'HTTP Error: ' . $httpCode, 'response' => $response];
    }
}
?>
