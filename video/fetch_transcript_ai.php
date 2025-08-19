<?php
/**
 * AI-based YouTube transcript fetcher using OpenAI Whisper API
 * This is a fallback when YouTube Transcript API fails
 */

header('Content-Type: application/json');

// Start session to access OAuth tokens
session_start();

// Include OAuth configuration
if (file_exists(__DIR__ . '/oauth-config.php')) {
    require_once __DIR__ . '/oauth-config.php';
} else {
    echo json_encode(['error' => 'OAuth configuration not found']);
    exit;
}

// Read the raw POST body and decode JSON
$body = file_get_contents('php://input');
$payload = json_decode($body, true);

if (!is_array($payload) || empty($payload['youtube_url'])) {
    echo json_encode(['error' => 'No youtube_url provided']);
    exit;
}

/**
 * Extracts a YouTube video ID from a URL.
 */
function getYouTubeVideoId(string $url): string
{
    $url = trim($url);
    
    $patterns = [
        '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
        '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
        '/youtube\.com\/watch\?.*[&?]v=([a-zA-Z0-9_-]{11})/',
        '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
        '/^([a-zA-Z0-9_-]{11})$/'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }
    
    return '';
}

$videoUrl = trim($payload['youtube_url']);
$videoId = getYouTubeVideoId($videoUrl);

if ($videoId === '') {
    echo json_encode(['error' => 'Invalid YouTube URL']);
    exit;
}

// Skip video info check - use default title
$videoTitle = "YouTube Video - " . $videoId;

// Method 1: Try to download audio using youtube-dl (if available)
$audioFile = null;
$tempDir = sys_get_temp_dir();

// Try using yt-dlp with correct path order
$ytdlpPaths = [
    '/home/nwengine/.local/bin/yt-dlp',  // ✅ This is the correct path!
    '/home/nwengine/bin/yt-dlp',
    '/usr/local/bin/yt-dlp',
    '/usr/bin/yt-dlp',
    'yt-dlp' // fallback to PATH
];

$output = '';
$ytdlpFound = false;

foreach ($ytdlpPaths as $ytdlpPath) {
    $ytdlpCommand = $ytdlpPath . " -x --audio-format mp3 --audio-quality 0 -o " . escapeshellarg($tempDir . "/%id%.%(ext)s") . " " . escapeshellarg($videoUrl);
    $output = shell_exec($ytdlpCommand . " 2>&1");
    
    if (!empty($output) && strpos($output, 'ERROR') === false && strpos($output, 'command not found') === false) {
        $ytdlpFound = true;
        break;
    }
}

if (!$ytdlpFound) {
    // Try youtube-dl as fallback
    $ytdlPaths = [
        '/usr/local/bin/youtube-dl',
        '/usr/bin/youtube-dl',
        '/home/nwengine/.local/bin/youtube-dl',
        'youtube-dl'
    ];
    
    foreach ($ytdlPaths as $ytdlPath) {
        $ytdlCommand = $ytdlPath . " -x --audio-format mp3 --audio-quality 0 -o " . escapeshellarg($tempDir . "/%id%.%(ext)s") . " " . escapeshellarg($videoUrl);
        $output = shell_exec($ytdlCommand . " 2>&1");
        
        if (!empty($output) && strpos($output, 'ERROR') === false && strpos($output, 'command not found') === false) {
            $ytdlpFound = true;
            break;
        }
    }
}

// Look for downloaded audio file
$audioFiles = glob($tempDir . "/" . $videoId . ".*");
if (!empty($audioFiles)) {
    $audioFile = $audioFiles[0];
}

if (!$audioFile) {
    echo json_encode([
        'error' => 'Failed to download audio',
        'debug' => [
            'ytdlp_output' => $output,
            'temp_dir' => $tempDir,
            'video_id' => $videoId
        ]
    ]);
    exit;
}

// Method 2: Use OpenAI Whisper API for transcription
$openaiApiKey = getenv('OPENAI_API_KEY'); // Set this in your environment
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
        'note' => 'Please set OPENAI_API_KEY in your environment or credentials.php'
    ]);
    exit;
}

// Upload audio file to OpenAI
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/audio/transcriptions');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $openaiApiKey
]);

// Create multipart form data
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

if ($httpCode !== 200 || !empty($curlError)) {
    echo json_encode([
        'error' => 'OpenAI API request failed',
        'debug' => [
            'http_code' => $httpCode,
            'curl_error' => $curlError,
            'response' => substr($response, 0, 500)
        ]
    ]);
    exit;
}

$transcriptData = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE || !isset($transcriptData['text'])) {
    echo json_encode([
        'error' => 'Failed to parse OpenAI response',
        'debug' => [
            'response' => $response,
            'json_error' => json_last_error_msg()
        ]
    ]);
    exit;
}

// Format transcript with timestamps
$formattedTranscript = '';
$totalLines = 0;

if (isset($transcriptData['words'])) {
    // Use word-level timestamps if available
    foreach ($transcriptData['words'] as $word) {
        $start = $word['start'];
        $text = $word['word'];
        
        $minutes = floor($start / 60);
        $seconds = $start % 60;
        $timeStamp = sprintf('[%02d:%02d]', $minutes, $seconds);
        $formattedTranscript .= $timeStamp . ' ' . $text . ' ';
        
        // Add line breaks every 10 words or at sentence boundaries
        if (strpos($text, '.') !== false || strpos($text, '!') !== false || strpos($text, '?') !== false) {
            $formattedTranscript .= "\n";
            $totalLines++;
        }
    }
} else {
    // Fallback to full text
    $formattedTranscript = $transcriptData['text'];
    $totalLines = substr_count($transcriptData['text'], "\n") + 1;
}

echo json_encode([
    'success' => true,
    'video_title' => $videoTitle,
    'video_id' => $videoId,
    'transcript_preview' => $formattedTranscript,
    'total_lines' => $totalLines,
    'message' => 'Transcript retrieved successfully via AI transcription!',
    'note' => 'Using OpenAI Whisper API for high-quality transcription.',
    'method' => 'ai_transcription',
    'language' => $transcriptData['language'] ?? 'unknown'
]);
?>
