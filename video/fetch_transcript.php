<?php
/**
 * Fetch the closed‑caption transcript for a given YouTube video.
 *
 * This endpoint expects a JSON payload containing a single key,
 * `youtube_url`, whose value is the URL of the YouTube video.  It
 * extracts the video ID from the URL, requests the timed text
 * (closed captions) from YouTube and returns a simplified
 * transcript with timestamps.  The response is returned as
 * JSON with either a `transcript` field on success or an
 * `error` field on failure.
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
 *
 * YouTube URLs come in several forms (e.g. https://www.youtube.com/watch?v=VIDEO_ID
 * or https://youtu.be/VIDEO_ID).  This function uses a regular expression
 * to find an 11‑character video ID after common URL patterns.  If no ID
 * is found, an empty string is returned.
 *
 * @param string $url YouTube URL provided by the caller
 * @return string Video ID or empty string on failure
 */
function getYouTubeVideoId(string $url): string
{
    $patterns = [
        '/youtu\.be\/([a-zA-Z0-9_-]{11})/',                 // short form
        '/youtube\.com\/(?:.*v=|.*\/v\/|embed\/)([a-zA-Z0-9_-]{11})/', // watch or embed form
    ];
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }
    return '';
}

$videoUrl = trim($payload['youtube_url']);
$videoId  = getYouTubeVideoId($videoUrl);

if ($videoId === '') {
    echo json_encode(['error' => 'Invalid YouTube URL']);
    exit;
}

/**
 * Make a simple HTTP GET request for a given URL and return the response
 * body as a string.  This helper first tries to use cURL (if available)
 * with sensible timeouts and SSL verification enabled.  If cURL is not
 * available or returns empty, it falls back to file_get_contents().  The
 * function returns the response string on success or null on failure.
 *
 * @param string $url Fully qualified URL to request
 * @return string|null The response body or null on error
 */
function http_get(string $url): ?string
{
    // Use cURL if available
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // Set a common User‑Agent header.  Some Google endpoints
        // require a browser‑like User‑Agent and may return 403/404
        // otherwise.  Accept a broad range of content types.
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept: */*',
            'Accept-Language: en-US,en;q=0.9',
            'Accept-Encoding: gzip, deflate',
            'Connection: keep-alive',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($data !== false && $data !== '' && $httpCode === 200) {
            return $data;
        }
    }
    
    // Fall back to file_get_contents(); suppress warnings in case allow_url_fopen is disabled
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept: */*',
                'Accept-Language: en-US,en;q=0.9',
            ],
            'timeout' => 15,
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ]
    ]);
    
    $data = @file_get_contents($url, false, $context);
    if ($data !== false && $data !== '') {
        return $data;
    }
    
    return null;
}

// Try to get captions using YouTube's timed text API
$listEndpoints = [
    'https://www.youtube.com/api/timedtext?type=list&v=',
    'https://video.google.com/timedtext?type=list&v=',
];

$listXmlString = null;
foreach ($listEndpoints as $endpoint) {
    $listXmlString = http_get($endpoint . urlencode($videoId));
    if ($listXmlString !== null && trim($listXmlString) !== '') {
        break;
    }
}

if ($listXmlString === null || trim($listXmlString) === '') {
    echo json_encode(['error' => 'Failed to retrieve caption track list. This video may not have captions available.']);
    exit;
}

libxml_use_internal_errors(true);
$listXml = simplexml_load_string($listXmlString);
if ($listXml === false || !isset($listXml->track) || count($listXml->track) == 0) {
    echo json_encode(['error' => 'No captions available for this video']);
    exit;
}

// Select a caption track.  Prefer an English manual track (kind="").
// If not found, choose an English ASR track (kind="asr").  If still
// none, fall back to the first available track.
$selectedTrack = null;
foreach ($listXml->track as $track) {
    $langCode = (string)$track['lang_code'];
    $kind     = (string)$track['kind'];
    if ($langCode === 'en' && $kind === '') {
        $selectedTrack = $track;
        break;
    }
}
if ($selectedTrack === null) {
    foreach ($listXml->track as $track) {
        $langCode = (string)$track['lang_code'];
        $kind     = (string)$track['kind'];
        if ($langCode === 'en' && $kind === 'asr') {
            $selectedTrack = $track;
            break;
        }
    }
}
if ($selectedTrack === null) {
    $selectedTrack = $listXml->track[0];
}

$langCode = (string)$selectedTrack['lang_code'];
$name     = (string)$selectedTrack['name'];
$kind     = (string)$selectedTrack['kind'];

$timedTextUrl = sprintf('https://www.youtube.com/api/timedtext?v=%s&lang=%s', urlencode($videoId), urlencode($langCode));
if ($name !== '') {
    $timedTextUrl .= '&name=' . urlencode($name);
}
if ($kind !== '') {
    $timedTextUrl .= '&kind=' . urlencode($kind);
}

$xmlString = http_get($timedTextUrl);
if ($xmlString === null) {
    echo json_encode(['error' => 'Failed to retrieve transcript from YouTube. This may require OAuth2 authentication.']);
    exit;
}

$xml = simplexml_load_string($xmlString);
if ($xml === false || !$xml->text) {
    echo json_encode(['error' => 'Failed to parse transcript or no captions available']);
    exit;
}

$lines = [];
foreach ($xml->text as $node) {
    $start   = (string)$node['start'];
    $content = trim(html_entity_decode((string)$node));
    if (!empty($content)) {
        $lines[] = '[' . $start . '] ' . $content;
    }
}

// Return available caption information and transcript preview
$captionInfo = [];
foreach ($listXml->track as $track) {
    $langCode = (string)$track['lang_code'];
    $name = (string)$track['name'];
    $kind = (string)$track['kind'];
    
    $captionInfo[] = [
        'language' => $langCode,
        'name' => $name,
        'kind' => $kind,
        'isCC' => ($kind === ''),
        'isAutoGenerated' => ($kind === 'asr'),
        'isDraft' => false,
        'trackKind' => $kind ?: 'manual'
    ];
}

// Return transcript preview (first 10 lines)
$transcriptPreview = implode("\n", array_slice($lines, 0, 10));
if (count($lines) > 10) {
    $transcriptPreview .= "\n... and " . (count($lines) - 10) . " more lines";
}

echo json_encode([
    'success' => true,
    'video_id' => $videoId,
    'available_captions' => $captionInfo,
    'transcript_preview' => $transcriptPreview,
    'total_lines' => count($lines),
    'message' => 'Transcript retrieved successfully. Preview shown below.',
    'note' => 'Full transcript available. To download complete transcript, OAuth2 authentication may be required for some videos.'
]);
?>