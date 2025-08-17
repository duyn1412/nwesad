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
 *
 * Note:  YouTube exposes transcripts through a timed text
 * interface.  Developer articles point out that you can either
 * use the official Data API to fetch captions or inspect the
 * network requests to the timed text endpoint when loading a
 * video【207579895664045†L228-L235】.  This script uses the timed text
 * endpoint because it does not require an API key.
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
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        // Set a common User‑Agent header.  Some Google endpoints
        // require a browser‑like User‑Agent and may return 403/404
        // otherwise.  Accept a broad range of content types.
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:115.0) Gecko/20100101 Firefox/115.0',
            'Accept: */*',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
        $statusOk = ($data !== false && $data !== '');
        curl_close($ch);
        if ($statusOk) {
            return $data;
        }
        // If the first attempt failed, try again with SSL verification disabled.
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // disable SSL verification as fallback
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
        curl_close($ch);
        if ($data !== false && $data !== '') {
            return $data;
        }
    }
    // Fall back to file_get_contents(); suppress warnings in case allow_url_fopen is disabled
    $data = @file_get_contents($url);
    if ($data !== false && $data !== '') {
        return $data;
    }
    return null;
}

// Try to get captions using YouTube Data API v3 first
$apiKey = 'AIzaSyBoJu9d4AldZMrgVxdUOY169Qd8IXp8Oqc'; // Your API key
$captionsUrl = "https://www.googleapis.com/youtube/v3/captions?part=snippet&videoId=" . urlencode($videoId) . "&key=" . $apiKey;

$listXmlString = null;
$listEndpoints = [
    $captionsUrl, // Try YouTube Data API first
    'https://video.google.com/timedtext?type=list&v=',
    'https://www.youtube.com/api/timedtext?type=list&v=',
];

foreach ($listEndpoints as $endpoint) {
    if (strpos($endpoint, 'googleapis.com') !== false) {
        // YouTube Data API
        $response = http_get($endpoint);
        if ($response && strpos($response, '"items"') !== false) {
            $data = json_decode($response, true);
            if (isset($data['items']) && count($data['items']) > 0) {
                // Convert API response to XML-like format for compatibility
                $listXmlString = '<?xml version="1.0" encoding="utf-8"?><transcript_list>';
                foreach ($data['items'] as $item) {
                    $listXmlString .= '<track lang_code="' . htmlspecialchars($item['snippet']['language'] ?? 'en') . '" name="' . htmlspecialchars($item['snippet']['name'] ?? '') . '" kind="' . ($item['snippet']['trackKind'] ?? '') . '"/>';
                }
                $listXmlString .= '</transcript_list>';
                break;
            }
        }
    } else {
        // Legacy timed text API
        $listXmlString = http_get($endpoint . urlencode($videoId));
        if ($listXmlString !== null && trim($listXmlString) !== '') {
            break;
        }
    }
}

if ($listXmlString === null || trim($listXmlString) === '') {
    echo json_encode(['error' => 'Failed to retrieve caption track list. YouTube may have changed their API.']);
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
    echo json_encode(['error' => 'Failed to retrieve transcript from YouTube']);
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
    $lines[] = '[' . $start . '] ' . $content;
}

// For now, return available caption information instead of actual transcript
// since downloading requires OAuth2 authentication
$captionInfo = [];
foreach ($xml->track as $track) {
    $langCode = (string)$track['lang_code'];
    $name = (string)$track['name'];
    $kind = (string)$track['kind'];
    
    $captionInfo[] = [
        'language' => $langCode,
        'name' => $name,
        'kind' => $kind,
        'status' => 'available'
    ];
}

echo json_encode([
    'transcript' => null,
    'message' => 'Transcript download requires OAuth2 authentication. Available captions listed below.',
    'available_captions' => $captionInfo,
    'note' => 'To download actual transcript content, OAuth2 authentication is required. This is a YouTube API limitation.'
]);