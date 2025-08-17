<?php
/**
 * Proxy endpoint for interacting with an OpenAI Assistant via the Assistants API.
 *
 * The Assistants API uses a conversational model based on threads, messages
 * and runs【284293684824040†L35-L46】.  To send a user query to an assistant you must
 * create a thread, add a message to the thread, create a run on the thread
 * specifying your assistant’s identifier, wait for the run to complete and
 * then retrieve the thread’s messages【284293684824040†L35-L46】.  This script
 * automates those steps using PHP’s cURL library.  It expects a JSON
 * payload with a `transcript` field, representing the content to send to
 * your assistant.  It returns a JSON object containing either the
 * assistant’s response or an error message.
 */

header('Content-Type: application/json');

/*
 * If you store your OpenAI credentials in a separate PHP file (for example,
 * a configuration or database access file), include it here so this script
 * can reuse the API key and assistant ID.  The included file should
 * populate either constants (`openai_api_key` and `video_assistant_id`)
 * or variables (`$openai_api_key` and `$video_assistant_id`).  For
 * example, you might have a file that queries a SQL database and sets
 * `$openai_api_key` and `$video_assistant_id`.
 */

session_start();
include '../connect-sql.php';
$activeTab  = isset($_SESSION['activeTab']) ? $_SESSION['activeTab'] : 'v-pills-home-tab';

$message = '';
//var_dump($_SESSION['admin']);
if ($_SESSION['admin'] != true) {

   exit('Access denied');

}

// Fallback definitions: if the constants are not already defined by
// the included file, attempt to define them using variables from
// the included file.  If neither is available, leave them empty so
// that an error is returned later.
if (!defined('openai_api_key')) {
    if (isset($openai_api_key)) {
        define('openai_api_key', $openai_api_key);
    } else {
        define('openai_api_key', '');
    }
}
if (!defined('video_assistant_id')) {
    if (isset($video_assistant_id)) {
        define('video_assistant_id', $video_assistant_id);
    } else {
        define('video_assistant_id', '');
    }
}

// Read and decode the JSON payload
$body    = file_get_contents('php://input');
$payload = json_decode($body, true);

if (!is_array($payload) || empty($payload['transcript'])) {
    echo json_encode(['error' => 'No transcript provided']);
    exit;
}

$transcript = $payload['transcript'];

// Ensure that the API key and assistant ID are available.  Without these
// credentials the request to OpenAI will fail.  If they are empty,
// return an error message immediately.
if (empty(openai_api_key) || empty(video_assistant_id)) {
    echo json_encode(['error' => 'OpenAI API key or Assistant ID is missing.']);
    exit;
}

// Compose the request payload for the chat completions API.  We
// provide a system prompt to instruct the assistant how to handle
// transcripts and a user message containing the transcript itself.
// Helper function to perform a cURL request with JSON data
function openai_request(string $method, string $url, array $data = null)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    $headers = [
        'Authorization: Bearer ' . openai_api_key,
        'Content-Type: application/json',
    ];
    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response    = curl_exec($ch);
    $statusCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return [false, null, $error];
    }
    curl_close($ch);
    return [$statusCode, json_decode($response, true), null];
}

// Step 1: Create a new thread.  According to the Assistants API, you must
// create a thread before adding messages【284293684824040†L35-L46】.
list($status, $threadResponse, $error) = openai_request('POST', 'https://api.openai.com/v1/threads', []);
if ($status !== 200 || empty($threadResponse['id'])) {
    $msg = $error ?: ($threadResponse['error']['message'] ?? 'Failed to create thread');
    echo json_encode(['error' => 'OpenAI thread creation error: ' . $msg]);
    exit;
}
$threadId = $threadResponse['id'];

// Step 2: Add the user’s transcript as a message in the thread【284293684824040†L35-L46】.
$messageData = [
    'role'    => 'user',
    'content' => $transcript,
];
list($status, $messageResponse, $error) = openai_request('POST', "https://api.openai.com/v1/threads/{$threadId}/messages", $messageData);
if ($status !== 200 || empty($messageResponse['id'])) {
    $msg = $error ?: ($messageResponse['error']['message'] ?? 'Failed to create message');
    echo json_encode(['error' => 'OpenAI message creation error: ' . $msg]);
    exit;
}

// Step 3: Start a run on the thread, specifying the assistant ID【284293684824040†L35-L46】.
$runData = [
    'assistant_id' => video_assistant_id,
];
list($status, $runResponse, $error) = openai_request('POST', "https://api.openai.com/v1/threads/{$threadId}/runs", $runData);
if ($status !== 200 || empty($runResponse['id'])) {
    $msg = $error ?: ($runResponse['error']['message'] ?? 'Failed to create run');
    echo json_encode(['error' => 'OpenAI run creation error: ' . $msg]);
    exit;
}
$runId = $runResponse['id'];

// Step 4: Poll the run status until it is completed or a timeout occurs.  The
// Assistants API uses asynchronous runs; you must query the run until its
// status becomes `completed`【284293684824040†L35-L46】.  We poll at 1‑second
// intervals for up to 30 seconds.
$attempts = 0;
$maxAttempts = 30;
$runStatus = $runResponse['status'] ?? 'queued';
while ($attempts < $maxAttempts && $runStatus !== 'completed' && $runStatus !== 'requires_action' && $runStatus !== 'failed' && $runStatus !== 'cancelled') {
    // Wait for a second before polling again
    sleep(1);
    $attempts++;
    list($status, $runResponse, $error) = openai_request('GET', "https://api.openai.com/v1/threads/{$threadId}/runs/{$runId}");
    if ($status !== 200) {
        $msg = $error ?: ($runResponse['error']['message'] ?? 'Failed to retrieve run status');
        echo json_encode(['error' => 'OpenAI run status error: ' . $msg]);
        exit;
    }
    $runStatus = $runResponse['status'] ?? 'unknown';
}

if ($runStatus !== 'completed') {
    echo json_encode(['error' => 'Assistant run did not complete in time (status: ' . $runStatus . ')']);
    exit;
}

// Step 5: Retrieve the messages from the thread and extract the assistant’s reply【284293684824040†L35-L46】.
list($status, $messagesResponse, $error) = openai_request('GET', "https://api.openai.com/v1/threads/{$threadId}/messages");
if ($status !== 200 || empty($messagesResponse['data'])) {
    $msg = $error ?: ($messagesResponse['error']['message'] ?? 'Failed to retrieve messages');
    echo json_encode(['error' => 'OpenAI message retrieval error: ' . $msg]);
    exit;
}

// The API returns messages with the most recent first.  Iterate through the
// messages to find the first assistant response.
$assistantReply = null;
foreach ($messagesResponse['data'] as $msg) {
    if ($msg['role'] === 'assistant') {
        // Each message may have multiple content parts.  We'll concatenate
        // any text parts.
        $content = '';
        foreach ($msg['content'] as $part) {
            if (isset($part['text']['value'])) {
                $content .= $part['text']['value'];
            }
        }
        $assistantReply = $content;
        break;
    }
}

if ($assistantReply === null) {
    echo json_encode(['error' => 'No assistant reply found in messages']);
    exit;
}

echo json_encode(['assistant_reply' => $assistantReply]);