<?php
/**
 * OAuth2 Callback Handler for YouTube API
 */

session_start();
require_once 'oauth-config.php';

// Check if authorization code is present
if (!isset($_GET['code'])) {
    die('Authorization code not found. Please try again.');
}

$authorizationCode = $_GET['code'];

try {
    // Exchange authorization code for access token
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $postData = [
        'client_id' => YOUTUBE_CLIENT_ID,
        'client_secret' => YOUTUBE_CLIENT_SECRET,
        'code' => $authorizationCode,
        'grant_type' => 'authorization_code',
        'redirect_uri' => YOUTUBE_REDIRECT_URI
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception('Token exchange failed. HTTP Code: ' . $httpCode . ' Response: ' . $response);
    }

    $tokenData = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON response from Google');
    }

    if (isset($tokenData['error'])) {
        throw new Exception('OAuth error: ' . $tokenData['error_description'] ?? $tokenData['error']);
    }

    // Store tokens in session
    $_SESSION['youtube_access_token'] = $tokenData['access_token'];
    if (isset($tokenData['refresh_token'])) {
        $_SESSION['youtube_refresh_token'] = $tokenData['refresh_token'];
    }
    $_SESSION['youtube_token_expires'] = time() + $tokenData['expires_in'];

    // Redirect back to test page with success message
    header('Location: test-oauth.php?oauth=success&message=Authentication successful!');
    exit();

} catch (Exception $e) {
    // Redirect back with error message
    $errorMessage = urlencode('OAuth error: ' . $e->getMessage());
    header('Location: test-oauth.php?oauth=error&message=' . $errorMessage);
    exit();
}
?>
al-scale=1.0">
    <title>OAuth2 Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">OAuth2 Authentication Error</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                        </div>
                        <p>There was an error during the OAuth2 authentication process.</p>
                        <a href="index.php" class="btn btn-primary">Return to Main Page</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
