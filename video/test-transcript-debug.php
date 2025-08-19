<?php
/**
 * Debug transcript response specifically
 */

header('Content-Type: application/json');

// Simulate the exact transcript response
$videoUrl = 'https://www.youtube.com/watch?v=Z2LgmIGE2nI';
$videoId = 'Z2LgmIGE2nI';
$videoTitle = "YouTube Video - " . $videoId;
$availableCaptions = [];

// Check if OpenAI API key is available
$openaiApiKey = getenv('OPENAI_API_KEY');
if (!$openaiApiKey) {
    // Try to read from credentials file
    if (file_exists(__DIR__ . '/../credentials.php')) {
        require_once __DIR__ . '/../credentials.php';
        $openaiApiKey = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : null;
    }
}

if (!$openaiApiKey) {
    echo json_encode([
        'error' => 'OpenAI API key not found',
        'note' => 'Please add OPENAI_API_KEY to your credentials.php file',
        'setup_guide' => 'See AI_TRANSCRIPTION_SETUP.md for setup instructions'
    ]);
    exit;
}

// Try AI transcription using OpenAI Whisper
$aiTranscriptUrl = "https://www.nwengineeringllc.com/nwesadmin/video/fetch_transcript_ai.php";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $aiTranscriptUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['youtube_url' => $videoUrl]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$aiResponse = curl_exec($ch);
$aiHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$aiCurlError = curl_error($ch);
curl_close($ch);

// Debug info
$debugInfo = [
    'ai_transcript_url' => $aiTranscriptUrl,
    'ai_http_code' => $aiHttpCode,
    'ai_curl_error' => $aiCurlError,
    'ai_response_length' => strlen($aiResponse),
    'ai_response_preview' => substr($aiResponse, 0, 500),
    'openai_api_key_exists' => !empty($openaiApiKey),
    'openai_api_key_length' => strlen($openaiApiKey ?? '')
];

if ($aiHttpCode === 200 && !empty($aiResponse)) {
    $aiData = json_decode($aiResponse, true);
    
    if (json_last_error() === JSON_ERROR_NONE && isset($aiData['success']) && $aiData['success']) {
        // AI transcription succeeded!
        echo json_encode([
            'success' => true,
            'video_title' => $videoTitle,
            'video_id' => $videoId,
            'available_captions' => $availableCaptions,
            'transcript_preview' => $aiData['transcript_preview'],
            'total_lines' => $aiData['total_lines'],
            'message' => 'Transcript retrieved successfully via AI transcription!',
            'note' => 'Using OpenAI Whisper API for high-quality transcription.',
            'method' => 'ai_transcription',
            'language' => $aiData['language'] ?? 'unknown',
            'debug_info' => $debugInfo
        ]);
        exit;
    } else {
        // JSON decode error
        echo json_encode([
            'success' => false,
            'error' => 'AI response JSON decode error',
            'json_error' => json_last_error_msg(),
            'raw_response' => $aiResponse,
            'debug_info' => $debugInfo
        ]);
        exit;
    }
} else {
    // AI transcription failed
    echo json_encode([
        'success' => false,
        'error' => 'AI transcription failed',
        'video_title' => $videoTitle,
        'video_id' => $videoId,
        'available_captions' => $availableCaptions,
        'message' => 'Failed to retrieve transcript via AI transcription.',
        'note' => 'Please check OpenAI API key and server configuration.',
        'debug_info' => $debugInfo
    ]);
    exit;
}
?>
