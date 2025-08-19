<?php
/**
 * Debug JSON response for transcript fetching
 */

header('Content-Type: application/json');

// Test JSON response
$testResponse = [
    'success' => true,
    'video_title' => 'Test Video',
    'video_id' => 'TEST123',
    'transcript_preview' => 'Test transcript content',
    'total_lines' => 10,
    'message' => 'Test message',
    'method' => 'ai_transcription',
    'language' => 'en'
];

// Test JSON encoding
$jsonResponse = json_encode($testResponse);

// Check for JSON errors
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'error' => 'JSON encoding error',
        'json_error' => json_last_error_msg(),
        'data' => $testResponse
    ]);
    exit;
}

// Test JSON decoding
$decoded = json_decode($jsonResponse, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'error' => 'JSON decoding error',
        'json_error' => json_last_error_msg(),
        'raw_json' => $jsonResponse
    ]);
    exit;
}

// Return clean JSON
echo $jsonResponse;
?>
