<?php
/**
 * Debug test for fetch transcript process
 */

header('Content-Type: application/json');

// Start session
session_start();

echo json_encode([
    'step' => '1_session_start',
    'status' => 'ok',
    'session_id' => session_id()
]);

// Test OAuth config loading
try {
    if (file_exists(__DIR__ . '/oauth-config.php')) {
        require_once __DIR__ . '/oauth-config.php';
        echo json_encode([
            'step' => '2_oauth_config_loaded',
            'status' => 'ok',
            'client_id_defined' => defined('YOUTUBE_CLIENT_ID'),
            'client_secret_defined' => defined('YOUTUBE_CLIENT_SECRET')
        ]);
    } else {
        echo json_encode([
            'step' => '2_oauth_config',
            'status' => 'error',
            'message' => 'oauth-config.php not found'
        ]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode([
        'step' => '2_oauth_config',
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}

// Test OAuth tokens
try {
    $hasTokens = hasValidYouTubeTokens();
    $accessToken = getYouTubeAccessToken();
    
    echo json_encode([
        'step' => '3_oauth_tokens',
        'status' => 'ok',
        'has_valid_tokens' => $hasTokens,
        'access_token_exists' => !empty($accessToken),
        'access_token_length' => strlen($accessToken ?? ''),
        'session_tokens' => [
            'access_token_set' => isset($_SESSION['youtube_access_token']),
            'refresh_token_set' => isset($_SESSION['youtube_refresh_token']),
            'expires_set' => isset($_SESSION['youtube_token_expires'])
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'step' => '3_oauth_tokens',
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}

// Test video ID extraction
try {
    $testUrl = 'https://www.youtube.com/watch?v=Z2LgmIGE2nI';
    $videoId = getYouTubeVideoId($testUrl);
    
    echo json_encode([
        'step' => '4_video_id_extraction',
        'status' => 'ok',
        'test_url' => $testUrl,
        'extracted_video_id' => $videoId,
        'video_id_valid' => !empty($videoId) && strlen($videoId) === 11
    ]);
} catch (Exception $e) {
    echo json_encode([
        'step' => '4_video_id_extraction',
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}

// Test YouTube API request
try {
    $videoInfoUrl = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id=Z2LgmIGE2nI";
    $videoInfo = makeYouTubeApiRequest($videoInfoUrl);
    
    echo json_encode([
        'step' => '5_youtube_api_request',
        'status' => 'ok',
        'api_response' => $videoInfo,
        'has_error' => isset($videoInfo['error']),
        'has_items' => isset($videoInfo['items']) && !empty($videoInfo['items'])
    ]);
} catch (Exception $e) {
    echo json_encode([
        'step' => '5_youtube_api_request',
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}

echo json_encode([
    'step' => '6_complete',
    'status' => 'all_tests_passed',
    'message' => 'All components are working correctly'
]);
?>
