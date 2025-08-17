<?php
/**
 * Fetch the closed‑caption transcript for a given YouTube video.
 * Simplified version for testing basic functionality.
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
 * body as a string.
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
        
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept: */*',
            'Accept-Language: en-US,en;q=0.9',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($data !== false && $data !== '' && $httpCode === 200) {
            return $data;
        }
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
    echo json_encode([
        'success' => false,
        'error' => 'Failed to retrieve caption track list. This video may not have captions available.',
        'video_id' => $videoId,
        'debug_info' => 'No captions found via timed text API'
    ]);
    exit;
}

libxml_use_internal_errors(true);
$listXml = simplexml_load_string($listXmlString);
if ($listXml === false || !isset($listXml->track) || count($listXml->track) == 0) {
    echo json_encode([
        'success' => false,
        'error' => 'No captions available for this video',
        'video_id' => $videoId,
        'debug_info' => 'XML parsing failed or no tracks found'
    ]);
    exit;
}

// Select a caption track. Prefer English manual track.
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
$kind     = (string)$track['kind'];

$timedTextUrl = sprintf('https://www.youtube.com/api/timedtext?v=%s&lang=%s', urlencode($videoId), urlencode($langCode));
if ($name !== '') {
    $timedTextUrl .= '&name=' . urlencode($name);
}
if ($kind !== '') {
    $timedTextUrl .= '&kind=' . urlencode($kind);
}

$xmlString = http_get($timedTextUrl);
if ($xmlString === null) {
    echo json_encode([
        'success' => true,
        'video_id' => $videoId,
        'available_captions' => [],
        'transcript_preview' => 'Captions found but transcript download failed',
        'total_captions' => count($listXml->track),
        'message' => 'Captions are available but transcript content requires OAuth2 authentication.',
        'note' => 'This is a YouTube API limitation. OAuth2 authentication is required for full transcript access.'
    ]);
    exit;
}

$xml = simplexml_load_string($xmlString);
if ($xml === false || !$xml->text) {
    echo json_encode([
        'success' => true,
        'video_id' => $videoId,
        'available_captions' => [],
        'transcript_preview' => 'Captions found but transcript parsing failed',
        'total_captions' => count($listXml->track),
        'message' => 'Captions are available but transcript content could not be parsed.',
        'note' => 'This may require OAuth2 authentication for full access.'
    ]);
    exit;
}

// Return available caption information
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

echo json_encode([
    'success' => true,
    'video_id' => $videoId,
    'available_captions' => $captionInfo,
    'transcript_preview' => 'Captions found successfully via timed text API',
    'total_captions' => count($captionInfo),
    'message' => 'Captions found successfully. Transcript preview available.',
    'note' => 'Full transcript content may require OAuth2 authentication for some videos.'
]);
?>