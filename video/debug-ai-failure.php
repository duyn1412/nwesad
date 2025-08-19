<?php
/**
 * Debug AI transcription failure
 */

header('Content-Type: application/json');

// Test video
$videoUrl = 'https://www.youtube.com/watch?v=Z2LgmIGE2nI';
$videoId = 'Z2LgmIGE2nI';
$tempDir = sys_get_temp_dir();

echo json_encode([
    'step' => '1_initialization',
    'video_url' => $videoUrl,
    'video_id' => $videoId,
    'temp_dir' => $tempDir
]);

// Step 2: Check OpenAI API key
$openaiApiKey = getenv('OPENAI_API_KEY');
if (!$openaiApiKey) {
    if (file_exists(__DIR__ . '/../credentials.php')) {
        require_once __DIR__ . '/../credentials.php';
        $openaiApiKey = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : null;
    }
}

echo json_encode([
    'step' => '2_openai_key_check',
    'openai_api_key_exists' => !empty($openaiApiKey),
    'openai_api_key_length' => strlen($openaiApiKey ?? ''),
    'openai_api_key_preview' => substr($openaiApiKey ?? '', 0, 10) . '...'
]);

if (!$openaiApiKey) {
    echo json_encode([
        'step' => '2_error',
        'error' => 'OpenAI API key not found'
    ]);
    exit;
}

// Step 3: Test yt-dlp
$ytdlpPaths = [
    '/usr/local/bin/yt-dlp',
    '/usr/bin/yt-dlp',
    '/home/nwengine/.local/bin/yt-dlp',
    '/home/nwengine/bin/yt-dlp',
    'yt-dlp'
];

$ytdlpFound = false;
$ytdlpOutput = '';

foreach ($ytdlpPaths as $ytdlpPath) {
    $testCommand = $ytdlpPath . " --version";
    $output = shell_exec($testCommand . " 2>&1");
    
    if (!empty($output) && strpos($output, 'command not found') === false) {
        $ytdlpFound = true;
        $ytdlpOutput = $output;
        break;
    }
}

echo json_encode([
    'step' => '3_ytdlp_check',
    'ytdlp_found' => $ytdlpFound,
    'ytdlp_path' => $ytdlpPath ?? 'none',
    'ytdlp_version' => trim($ytdlpOutput)
]);

if (!$ytdlpFound) {
    echo json_encode([
        'step' => '3_error',
        'error' => 'yt-dlp not found'
    ]);
    exit;
}

// Step 4: Test audio download
$ytdlpCommand = $ytdlpPath . " -x --audio-format mp3 --audio-quality 0 -o " . escapeshellarg($tempDir . "/%id%.%(ext)s") . " " . escapeshellarg($videoUrl);
$downloadOutput = shell_exec($ytdlpCommand . " 2>&1");

echo json_encode([
    'step' => '4_audio_download',
    'ytdlp_command' => $ytdlpCommand,
    'download_output' => $downloadOutput,
    'download_success' => !empty($downloadOutput) && strpos($downloadOutput, 'ERROR') === false
]);

// Step 5: Check downloaded file
$audioFiles = glob($tempDir . "/" . $videoId . ".*");
$audioFile = !empty($audioFiles) ? $audioFiles[0] : null;

echo json_encode([
    'step' => '5_audio_file_check',
    'audio_file_exists' => !empty($audioFile),
    'audio_file_path' => $audioFile,
    'audio_file_size' => $audioFile ? filesize($audioFile) : 0
]);

if (!$audioFile) {
    echo json_encode([
        'step' => '5_error',
        'error' => 'Audio file not downloaded'
    ]);
    exit;
}

// Step 6: Test OpenAI API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/audio/transcriptions');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $openaiApiKey
]);

$postData = [
    'file' => new CURLFile($audioFile),
    'model' => 'whisper-1',
    'response_format' => 'verbose_json',
    'timestamp_granularities' => 'word'
];

curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Clean up audio file
if (file_exists($audioFile)) {
    unlink($audioFile);
}

echo json_encode([
    'step' => '6_openai_api_test',
    'http_code' => $httpCode,
    'curl_error' => $curlError,
    'response_length' => strlen($response),
    'response_preview' => substr($response, 0, 200),
    'success' => $httpCode === 200
]);

if ($httpCode !== 200) {
    echo json_encode([
        'step' => '6_error',
        'error' => 'OpenAI API request failed',
        'http_code' => $httpCode,
        'response' => $response
    ]);
    exit;
}

// Step 7: Parse response
$transcriptData = json_decode($response, true);
$jsonError = json_last_error();

echo json_encode([
    'step' => '7_parse_response',
    'json_error' => $jsonError,
    'json_error_msg' => json_last_error_msg(),
    'has_text' => isset($transcriptData['text']),
    'text_length' => strlen($transcriptData['text'] ?? ''),
    'success' => $jsonError === JSON_ERROR_NONE && isset($transcriptData['text'])
]);

echo json_encode([
    'step' => '8_complete',
    'success' => true,
    'transcript_preview' => substr($transcriptData['text'] ?? '', 0, 100)
]);
?>
