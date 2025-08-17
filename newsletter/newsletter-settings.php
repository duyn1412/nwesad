<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Start the session
session_start();

// Include your database connection file
include '../connect-sql.php';
include __DIR__ . '/get-newsletter-settings.php';
include '../get-settings.php';
// include '../vendor/autoload.php';

// use hiddenhatpress\openai\assistants\Assistants;
// use hiddenhatpress\openai\assistants\AsstComms;

//$newsletter_assistant_i

//putenv('openai_api_key=' . $openai_api_key);



// Function to create a thread with messages
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

// Function to run the assistant on a thread
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

// Function to get messages for a thread
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

function getLinkDetails($url) {
    // Check if the URL is empty
    if (empty($url)) {
        throw new InvalidArgumentException('URL cannot be empty');
    }

    // Create a new DOM Document
    $doc = new DOMDocument();

    // Suppress warnings due to malformed HTML
    @ $doc->loadHTMLFile($url);

    // Create a new XPath object
    $xpath = new DOMXPath($doc);

    // Get the title tag
    $title = $xpath->query('//title')->item(0)->nodeValue;

    // // Get the og:image tag
    // $ogImage = $xpath->query('//meta[@property="og:image"]/@content')->item(0)->nodeValue;

    // Get the og:image tag
    $ogImage = $xpath->query('//meta[@property="og:image"]/@content')->item(0);
    $ogImage = $ogImage ? $ogImage->nodeValue : null;

    // If og:image does not provide an image URL, use og:image:url instead
    if (!$ogImage) {
        $ogImage = $xpath->query('//meta[@property="og:image:url"]/@content')->item(0);
        $ogImage = $ogImage ? $ogImage->nodeValue : null;
    }

    // Return the title and og:image
    return array($title, $ogImage);
}





// Replace with your API key and assistant ID
$apiKey = $openai_api_key;
$assistantId = $newsletter_assistant_id ;

// Get user input for links
$LINK_URL_1 = $_POST['LINK_URL_1'] ?? '';
$LINK_URL_2 = $_POST['LINK_URL_2'] ?? '';
$LINK_URL_3 = $_POST['LINK_URL_3'] ?? '';
$VIDEO_ID = $_POST['VIDEO_ID'] ?? '';


// Get the form data
$TOP_HEADER_TXT = $_POST['TOP_HEADER_TXT'] ?? '';
$TOP_TEXT_TXT = $_POST['TOP_TEXT_TXT'] ?? '';

// Initialize all variables to prevent undefined variable errors
$LINK_TEXT_1 = '';
$LINK_TEXT_2 = '';
$LINK_TEXT_3 = '';
$LINK_OG_TITLE_1 = '';
$LINK_OG_TITLE_2 = '';
$LINK_OG_TITLE_3 = '';
$LINK_OG_IMG_1 = '';
$LINK_OG_IMG_2 = '';
$LINK_OG_IMG_3 = '';


// Prepare a single message with all links
$messages = [
    [
        "role" => "user",
        "content" => "Analyze these links:\n1. $LINK_URL_1\n2. $LINK_URL_2\n3. $LINK_URL_3. Provide insights for each link in order."
    ]
];

// Step 1: Create a thread with the single message
$thread = createThreadWithMessages($apiKey, $messages);
if (isset($thread['id'])) {
    $threadId = $thread['id'];
   // echo "Thread Created: $threadId\n";

    // Step 2: Run the assistant on the thread
    $run = runAssistant($apiKey, $threadId, $assistantId);
    if (isset($run['id'])) {
      //  echo "Assistant Run Initiated: $run[id]\n";

        // Step 3: Retrieve messages (response from the assistant)
        sleep(2); // Wait a few seconds for the assistant to complete the run
        $responseMessages = getMessages($apiKey, $threadId);

        if (isset($responseMessages['data']) && count($responseMessages['data']) > 0) {
          //  echo "[Assistant Response]:\n";
        
            foreach ($responseMessages['data'] as $message) {
                if ($message['role'] === 'assistant') {
                    if (isset($message['content'][0]['text']['value'])) {
                        // Get the full response text
                        $responseText = $message['content'][0]['text']['value'];
                        //echo $responseText . "\n";
        
                        // Split response into parts (based on numbered sections)
                        $insights = preg_split('/\d\.\s/', $responseText, -1, PREG_SPLIT_NO_EMPTY);
        
                        // Assign each insight to a variable
                        $LINK_TEXT_1 = $insights[0] ?? "No response received.";
                        $LINK_TEXT_2 = $insights[1] ?? "No response received.";
                        $LINK_TEXT_3 = $insights[2] ?? "No response received.";
                    }
                }
            }


            // Get link details with error handling
            try {
                list($LINK_OG_TITLE_1, $LINK_OG_IMG_1) = getLinkDetails($LINK_URL_1);
            } catch (Exception $e) {
                $LINK_OG_TITLE_1 = '';
                $LINK_OG_IMG_1 = '';
            }
            
            try {
                list($LINK_OG_TITLE_2, $LINK_OG_IMG_2) = getLinkDetails($LINK_URL_2);
            } catch (Exception $e) {
                $LINK_OG_TITLE_2 = '';
                $LINK_OG_IMG_2 = '';
            }
            
            try {
                list($LINK_OG_TITLE_3, $LINK_OG_IMG_3) = getLinkDetails($LINK_URL_3);
            } catch (Exception $e) {
                $LINK_OG_TITLE_3 = '';
                $LINK_OG_IMG_3 = '';
            }

            // Get the username from the cookie
            $username = $_COOKIE['username'];
            // Prepare an SQL statement to get the user_id
            $stmt = $conn->prepare("SELECT id FROM nwengine_user WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($user_id);
            $stmt->fetch();
            $stmt->close(); // Close the statement

            // Prepare an SQL statement to check if a row already exists
            $stmt = $conn->prepare("SELECT * FROM new_settings WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->close(); // Close the statement
                // A row already exists, so update it (temporarily without VIDEO_ID until column is added)
                $stmt = $conn->prepare("UPDATE new_settings SET TOP_HEADER_TXT = ?, TOP_TEXT_TXT = ?, LINK_URL_1 = ?, LINK_TEXT_1 = ?, LINK_OG_TITLE_1 = ?, LINK_OG_IMG_1 = ?, LINK_URL_2 = ?, LINK_TEXT_2 = ?, LINK_OG_TITLE_2 = ?, LINK_OG_IMG_2 = ?, LINK_URL_3 = ?, LINK_TEXT_3 = ?, LINK_OG_TITLE_3 = ?, LINK_OG_IMG_3 = ?, VIDEO_ID = ? WHERE user_id = ?");
                $stmt->bind_param("sssssssssssssssi", $TOP_HEADER_TXT, $TOP_TEXT_TXT, $LINK_URL_1, $LINK_TEXT_1, $LINK_OG_TITLE_1, $LINK_OG_IMG_1, $LINK_URL_2, $LINK_TEXT_2, $LINK_OG_TITLE_2, $LINK_OG_IMG_2, $LINK_URL_3, $LINK_TEXT_3, $LINK_OG_TITLE_3, $LINK_OG_IMG_3, $VIDEO_ID, $user_id);
            } else {
                $stmt->close(); // Close the statement
                // No row exists, so insert a new one (temporarily without VIDEO_ID until column is added)
                $stmt = $conn->prepare("INSERT INTO new_settings (TOP_HEADER_TXT, TOP_TEXT_TXT, LINK_URL_1, LINK_TEXT_1, LINK_OG_TITLE_1, LINK_OG_IMG_1, LINK_URL_2, LINK_TEXT_2, LINK_OG_TITLE_2, LINK_OG_IMG_2, LINK_URL_3, LINK_TEXT_3, LINK_OG_TITLE_3, LINK_OG_IMG_3, VIDEO_ID, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssssssssssi", $TOP_HEADER_TXT, $TOP_TEXT_TXT, $LINK_URL_1, $LINK_TEXT_1, $LINK_OG_TITLE_1, $LINK_OG_IMG_1, $LINK_URL_2, $LINK_TEXT_2, $LINK_OG_TITLE_2, $LINK_OG_IMG_2, $LINK_URL_3, $LINK_TEXT_3, $LINK_OG_TITLE_3, $LINK_OG_IMG_3, $VIDEO_ID, $user_id);
            }

            // Execute the statement
            if ($stmt->execute()) {
                // Set a session variable with the success message
                $_SESSION['message'] = "Settings saved successfully.";
            } else {
                // Set a session variable with the error message
                $_SESSION['message'] = "Error: " . $stmt->error;
            }

            $stmt->close(); // Close the statement

            // Redirect to the current page
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;


            // Close the statement
            $stmt->close();

            // Close the database connection
            $conn->close();

        
            // Print results
            // echo "\nResults:\n";
            // echo "LINK_TEXT_1: $LINK_TEXT_1\n";
            // echo "LINK_TEXT_2: $LINK_TEXT_2\n";
            // echo "LINK_TEXT_3: $LINK_TEXT_3\n";
        }
    } else {
        echo "Failed to run the assistant.\n";
    }
} else {
    echo "Failed to create a thread.\n";
}



