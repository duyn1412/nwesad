<?php
/**
 * Test YouTube API calls step by step
 */
header('Content-Type: text/html; charset=utf-8');

// Start session and include OAuth config
session_start();
require_once __DIR__ . '/oauth-config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>YouTube API Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .step { margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; }
        .warning { background-color: #fff3cd; border-color: #ffeaa7; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>YouTube API Test - Step by Step</h1>
    
    <?php
    // Test 1: Check OAuth tokens
    echo '<div class="step info">';
    echo '<h3>Test 1: OAuth Tokens Check</h3>';
    
    if (hasValidYouTubeTokens()) {
        echo '<span class="success">✅ OAuth tokens are valid</span><br>';
        $accessToken = getYouTubeAccessToken();
        echo 'Access token: ' . substr($accessToken, 0, 20) . '...<br>';
    } else {
        echo '<span class="error">❌ OAuth tokens are invalid</span><br>';
        echo 'Auth URL: <a href="' . getYouTubeAuthUrl() . '" target="_blank">Click here to authenticate</a><br>';
    }
    echo '</div>';
    
    // Test 2: Test simple YouTube API call
    if (hasValidYouTubeTokens()) {
        echo '<div class="step info">';
        echo '<h3>Test 2: Simple YouTube API Call</h3>';
        
        $testVideoId = 'dQw4w9WgXcQ'; // Rick Roll video (should have captions)
        $apiUrl = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id=" . urlencode($testVideoId);
        
        echo 'Testing API URL: ' . htmlspecialchars($apiUrl) . '<br>';
        
        // Make the API call
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        echo 'Making cURL request...<br>';
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        echo 'HTTP Code: ' . $httpCode . '<br>';
        
        if ($curlError) {
            echo '<span class="error">❌ cURL Error: ' . htmlspecialchars($curlError) . '</span><br>';
        } else {
            echo '<span class="success">✅ cURL request completed</span><br>';
        }
        
        if ($httpCode === 200) {
            echo '<span class="success">✅ API call successful</span><br>';
            $data = json_decode($response, true);
            if ($data && isset($data['items'][0]['snippet']['title'])) {
                echo 'Video title: ' . htmlspecialchars($data['items'][0]['snippet']['title']) . '<br>';
            }
            echo '<details><summary>Full API Response</summary><pre>' . htmlspecialchars($response) . '</pre></details>';
        } else {
            echo '<span class="error">❌ API call failed with HTTP ' . $httpCode . '</span><br>';
            echo '<details><summary>Error Response</summary><pre>' . htmlspecialchars($response) . '</pre></details>';
        }
        
        echo '</div>';
        
        // Test 3: Test captions API call
        echo '<div class="step info">';
        echo '<h3>Test 3: Captions API Call</h3>';
        
        $captionsUrl = "https://www.googleapis.com/youtube/v3/captions?part=snippet&videoId=" . urlencode($testVideoId);
        echo 'Testing captions API URL: ' . htmlspecialchars($captionsUrl) . '<br>';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $captionsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        echo 'Making captions API call...<br>';
        $captionsResponse = curl_exec($ch);
        $captionsHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $captionsCurlError = curl_error($ch);
        curl_close($ch);
        
        echo 'Captions HTTP Code: ' . $captionsHttpCode . '<br>';
        
        if ($captionsCurlError) {
            echo '<span class="error">❌ Captions cURL Error: ' . htmlspecialchars($captionsCurlError) . '</span><br>';
        } else {
            echo '<span class="success">✅ Captions cURL request completed</span><br>';
        }
        
        if ($captionsHttpCode === 200) {
            echo '<span class="success">✅ Captions API call successful</span><br>';
            $captionsData = json_decode($captionsResponse, true);
            if ($captionsData && isset($captionsData['items'])) {
                echo 'Available captions: ' . count($captionsData['items']) . '<br>';
                foreach ($captionsData['items'] as $caption) {
                    $snippet = $caption['snippet'];
                    echo '- Language: ' . ($snippet['language'] ?? 'unknown') . 
                         ', Name: ' . ($snippet['name'] ?? 'none') . 
                         ', Track Kind: ' . ($snippet['trackKind'] ?? 'unknown') . '<br>';
                }
            }
            echo '<details><summary>Captions API Response</summary><pre>' . htmlspecialchars($captionsResponse) . '</pre></details>';
        } else {
            echo '<span class="error">❌ Captions API call failed with HTTP ' . $captionsHttpCode . '</span><br>';
            echo '<details><summary>Captions Error Response</summary><pre>' . htmlspecialchars($captionsResponse) . '</pre></details>';
        }
        
        echo '</div>';
    }
    ?>
    
    <div class="step warning">
        <h3>Next Steps:</h3>
        <p>If all tests pass, the issue might be in the main fetch_transcript.php file logic.</p>
        <p>If any test fails, we know exactly where the problem is.</p>
    </div>
</body>
</html>
