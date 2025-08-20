<?php
/**
 * Alternative transcript fetcher using external services
 * When YouTube download fails
 */

header('Content-Type: application/json');

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

$videoTitle = "YouTube Video - " . $videoId;

// Method 1: Try YouTube Transcript API (different endpoints)
$transcriptEndpoints = [
    "https://youtube-transcript-api.vercel.app/api/transcript?url=" . urlencode($videoUrl),
    "https://youtube-transcript-api.vercel.app/api/transcript?videoID=" . urlencode($videoId) . "&lang=en",
    "https://youtube-transcript-api.vercel.app/api/transcript?videoID=" . urlencode($videoId)
];

$transcriptFound = false;
$transcriptData = null;

foreach ($transcriptEndpoints as $endpoint) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && !empty($response)) {
        $data = json_decode($response, true);
        
        if (json_last_error() === JSON_ERROR_NONE && isset($data['transcript'])) {
            $transcriptFound = true;
            $transcriptData = $data;
            break;
        }
    }
}

if ($transcriptFound) {
    // Format transcript with timestamps
    $transcriptLines = $transcriptData['transcript'];
    $totalLines = count($transcriptLines);
    
    $formattedTranscript = '';
    foreach ($transcriptLines as $line) {
        $start = $line['start'] ?? 0;
        $text = $line['text'] ?? '';
        
        if (!empty(trim($text))) {
            $minutes = floor($start / 60);
            $seconds = $start % 60;
            $timeStamp = sprintf('[%02d:%02d]', $minutes, $seconds);
            $formattedTranscript .= $timeStamp . ' ' . trim($text) . "\n";
        }
    }
    
    echo json_encode([
        'success' => true,
        'video_title' => $videoTitle,
        'video_id' => $videoId,
        'transcript_preview' => $formattedTranscript,
        'total_lines' => $totalLines,
        'message' => 'Transcript retrieved successfully via external service!',
        'note' => 'Using YouTube Transcript API as fallback.',
        'method' => 'external_transcript_api',
        'language' => 'en'
    ]);
    exit;
}

// Method 2: Return helpful information
echo json_encode([
    'success' => false,
    'error' => 'All transcript methods failed',
    'video_title' => $videoTitle,
    'video_id' => $videoId,
    'message' => 'Unable to retrieve transcript from this video.',
    'note' => 'YouTube download tools are blocked. Consider manual transcription.',
    'suggestions' => [
        'Use external transcription services (AssemblyAI, Google Speech-to-Text)',
        'Try different video or check if captions are available',
        'Manual transcription for important content'
    ],
    'debug_info' => [
        'video_url' => $videoUrl,
        'video_id' => $videoId,
        'transcript_endpoints_tried' => count($transcriptEndpoints),
        'note' => 'YouTube restrictions prevent automated download'
    ]
]);
?>
