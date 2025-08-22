<?php
/**
 * Video OpenAI Assistant Integration
 * Based on newsletter implementation pattern
 */

header('Content-Type: application/json');

// Get OpenAI API key and Assistant ID - use same pattern as fetch_transcript.php
$openaiApiKey = getenv('OPENAI_API_KEY');
$videoAssistantId = getenv('VIDEO_ASSISTANT_ID');

// Fallback to credentials.php if environment variables not set
if (!$openaiApiKey || !$videoAssistantId) {
    // Include credentials file - look in multiple locations like oauth-config.php
    $credentialsPath = __DIR__ . '/../credentials.php';
    if (!file_exists($credentialsPath)) {
        // Try alternative paths
        $alternativePaths = [
            __DIR__ . '/credentials.php',  // Same directory
            dirname(__DIR__) . '/credentials.php',  // Root directory
            '/home/nwengine/public_html/nwesadmin/credentials.php'  // Absolute path
        ];
        
        $found = false;
        foreach ($alternativePaths as $path) {
            if (file_exists($path)) {
                $credentialsPath = $path;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            echo json_encode(['error' => 'Credentials file not found. Please create this file with your OpenAI credentials.']);
            exit;
        }
    }
    
    require_once $credentialsPath;
    
    // Get from constants if environment variables not available
    $openaiApiKey = $openaiApiKey ?: ($OPENAI_API_KEY ?? null);
    $videoAssistantId = $videoAssistantId ?: ($VIDEO_ASSISTANT_ID ?? null);
}

if (!$openaiApiKey || !$videoAssistantId) {
    echo json_encode([
        'error' => 'OpenAI API key or Video Assistant ID not found',
        'note' => 'Please set OPENAI_API_KEY and VIDEO_ASSISTANT_ID in credentials.php'
    ]);
    exit;
}

// Read the raw POST body and decode JSON
$body = file_get_contents('php://input');
$payload = json_decode($body, true);

if (!is_array($payload) || empty($payload['transcript'])) {
    echo json_encode(['error' => 'No transcript provided']);
    exit;
}

$transcript = $payload['transcript'];

/**
 * Function to create a thread with messages
 */
function createThreadWithMessages($apiKey, $messages) {
    $url = "https://api.openai.com/v1/threads";

    $payload = [
        "messages" => $messages
    ];

    $headers = [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json",
        "OpenAI-Beta: assistants=v2"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

/**
 * Function to run the assistant on a thread
 */
function runAssistant($apiKey, $threadId, $assistantId) {
    $url = "https://api.openai.com/v1/threads/$threadId/runs";

    $payload = [
        "assistant_id" => $assistantId
    ];

    $headers = [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json",
        "OpenAI-Beta: assistants=v2"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

/**
 * Function to get messages for a thread
 */
function getMessages($apiKey, $threadId) {
    $url = "https://api.openai.com/v1/threads/$threadId/messages";

    $headers = [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json",
        "OpenAI-Beta: assistants=v2"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

/**
 * Function to wait for run completion
 */
function waitForRunCompletion($apiKey, $threadId, $runId) {
    $maxAttempts = 30; // Maximum 30 attempts (30 seconds)
    $attempts = 0;
    
    while ($attempts < $maxAttempts) {
        $url = "https://api.openai.com/v1/threads/$threadId/runs/$runId";
        
        $headers = [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json",
            "OpenAI-Beta: assistants=v2"
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $runData = json_decode($response, true);
        
        if (isset($runData['status'])) {
            if ($runData['status'] === 'completed') {
                return true;
            } elseif ($runData['status'] === 'failed' || $runData['status'] === 'cancelled') {
                return false;
            }
        }
        
        sleep(1); // Wait 1 second before next check
        $attempts++;
    }
    
    return false; // Timeout
}

// Prepare message with transcript
$messages = [
    [
        "role" => "user",
        "content" => "Analyze this video transcript and provide insights:\n\n$transcript"
    ]
];

try {
    // Step 1: Create a thread with the transcript message
    $thread = createThreadWithMessages($openaiApiKey, $messages);
    
    if (!isset($thread['id'])) {
        throw new Exception('Failed to create thread: ' . json_encode($thread));
    }
    
    $threadId = $thread['id'];
    
    // Step 2: Run the assistant on the thread
    $run = runAssistant($openaiApiKey, $threadId, $videoAssistantId);
    
    if (!isset($run['id'])) {
        throw new Exception('Failed to run assistant: ' . json_encode($run));
    }
    
    $runId = $run['id'];
    
    // Step 3: Wait for the run to complete
    if (!waitForRunCompletion($openaiApiKey, $threadId, $runId)) {
        throw new Exception('Assistant run did not complete in time');
    }
    
    // Step 4: Retrieve messages (response from the assistant)
    $responseMessages = getMessages($openaiApiKey, $threadId);
    
    if (!isset($responseMessages['data']) || count($responseMessages['data']) === 0) {
        throw new Exception('No response messages received');
    }
    
    // Find the assistant's response
    $assistantResponse = '';
    foreach ($responseMessages['data'] as $message) {
        if ($message['role'] === 'assistant') {
            if (isset($message['content'][0]['text']['value'])) {
                $assistantResponse = $message['content'][0]['text']['value'];
                break;
            }
        }
    }
    
    if (empty($assistantResponse)) {
        throw new Exception('No assistant response content found');
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'response' => $assistantResponse,
        'thread_id' => $threadId,
        'run_id' => $runId,
        'message' => 'Video transcript analyzed successfully by OpenAI Assistant'
    ]);
    
} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'thread_id' => $threadId ?? null,
            'run_id' => $runId ?? null,
            'assistant_id' => $videoAssistantId
        ]
    ]);
}
?>
