<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../connect-sql.php';
// Get the username from the cookie
$username = $_COOKIE['username'];

//var_dump($_COOKIE['username']);
$stmt = $conn->prepare("SELECT id FROM nwengine_user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close(); // Close the statement

//var_dump($user_id);

// $link1 = 'https://intelligence.supplyframe.com/nasa-designs-new-ventilator-in-37-days-a-supplyframe-sourcing-story/'; // Assuming the URL is coming from a form submission

// $link2 = 'https://intelligence.supplyframe.com/buyers-electronics-supply-chain/';

// $link3 = 'https://intelligence.supplyframe.com/h2-mixed-market-optimism-doubt/';

// include __DIR__ . '/get-newsletter-settings.php';

// include '../get-settings.php';
// include '../vendor/autoload.php';
// use hiddenhatpress\openai\assistants\Assistants;
// use hiddenhatpress\openai\assistants\AsstComms;

// //$newsletter_assistant_i

// putenv('openai_api_key=' . $openai_api_key);

// $token = getenv('openai_api_key');
// // this model needed for retrieval tools
// $model = "gpt-3.5-turbo";
// $asstcomms = new AsstComms($model, $token);
// $assistants = new Assistants($asstcomms);
// $asstservice = $assistants->getAssistantService();
// $fileservice  = $assistants->getAssistantFileService();
// $threadservice = $assistants->getThreadService();
// $runservice = $assistants->getRunService();
// $messageservice = $assistants->getMessageService();

// // will get 20 by default
// $entities = $asstservice->list();
// $assistantid = $newsletter_assistant_id;
// $name = "Newsletter-Generator";

// foreach ($entities['data'] as $asst) {
//     if ($asst['name'] == $name) {
//         $assistantid = $asst['id'];
//        // var_dump($assistantid);
//     }
// }


// $links = array($link1,$link2,$link3);
// $i = 1;
// foreach ($links as $link) {
//     // Create a new thread
//     $threadresp = $threadservice->create();   
//     $threadid = $threadresp['id'];

//     // Create a message and add to the thread
//     $content = $link;
//     $msgresp = $messageservice->create($threadid, $content);

//     // Run the assistant and wait for it to complete
//     $runresp = $runservice->create($threadid, $assistantid);
//     while($runresp['status'] != "completed") {
//         sleep(1);
//         $runresp = $runservice->retrieve($threadid, $runresp['id']);
//     }

//     // Access the response
//     $msgs = $messageservice->listMessages($threadid);

//     // Print the response messages
//     foreach ($msgs as $msg) {
      
//         $LINK_TEXT_.$i =  $msgs['data'][0]['content'][0]['text']['value'];
//     }
//     $i++;
// }


// // create a thread
// $threadresp = $threadservice->create();   
// $threadid = $threadresp['id'];

// // create a message and add to the thread
// $content = $link1 ;
// $msgresp = $messageservice->create($threadid, $content);


// $runresp = $runservice->create($threadid, $assistantid);
// while($runresp['status'] != "completed") {
//     sleep(1);
//     //print "# polling {$runresp['status']}\n";
//     $runresp = $runservice->retrieve($threadid, $runresp['id']);
// }

// // access the response
// $msgs = $messageservice->listMessages($threadid);


