<?php
/**
 * OAuth2 Test Page for YouTube API
 */

session_start();
require_once 'oauth-config.php';

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['youtube_access_token']);
    unset($_SESSION['youtube_refresh_token']);
    unset($_SESSION['youtube_token_expires']);
    header('Location: test-oauth.php');
    exit();
}

// Check if user is already authenticated
$isAuthenticated = hasValidYouTubeTokens();
$accessToken = getYouTubeAccessToken();
$refreshToken = isset($_SESSION['youtube_refresh_token']) ? $_SESSION['youtube_refresh_token'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouTube OAuth2 Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>YouTube OAuth2 Authentication Test</h4>
                    </div>
                    <div class="card-body">
                        
                        <?php if (isset($_GET['oauth']) && $_GET['oauth'] === 'success'): ?>
                            <div class="alert alert-success">
                                <strong>Success!</strong> 
                                <?php echo htmlspecialchars($_GET['message'] ?? 'Authentication successful!'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['oauth']) && $_GET['oauth'] === 'error'): ?>
                            <div class="alert alert-danger">
                                <strong>Error!</strong> 
                                <?php echo htmlspecialchars($_GET['message'] ?? 'Authentication failed!'); ?>
                            </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <h5>Current Status:</h5>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Authentication Status
                                    <span class="badge bg-<?php echo $isAuthenticated ? 'success' : 'secondary'; ?> rounded-pill">
                                        <?php echo $isAuthenticated ? 'Authenticated' : 'Not Authenticated'; ?>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Access Token
                                    <span class="badge bg-<?php echo $accessToken ? 'success' : 'secondary'; ?> rounded-pill">
                                        <?php echo $accessToken ? 'Present' : 'Missing'; ?>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Refresh Token
                                    <span class="badge bg-<?php echo $refreshToken ? 'success' : 'secondary'; ?> rounded-pill">
                                        <?php echo $refreshToken ? 'Present' : 'Missing'; ?>
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <?php if (!$isAuthenticated): ?>
                            <div class="mb-4">
                                <h5>OAuth2 Configuration:</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Client ID:</strong></td>
                                            <td><code><?php echo htmlspecialchars(YOUTUBE_CLIENT_ID); ?></code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Redirect URI:</strong></td>
                                            <td><code><?php echo htmlspecialchars(YOUTUBE_REDIRECT_URI); ?></code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Scopes:</strong></td>
                                            <td><code><?php echo htmlspecialchars(implode(', ', YOUTUBE_SCOPES)); ?></code></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="<?php echo getYouTubeAuthUrl(); ?>" class="btn btn-primary btn-lg">
                                    <i class="bi bi-google"></i> Sign in with Google
                                </a>
                                <small class="text-muted text-center">
                                    This will redirect you to Google for authorization
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="mb-4">
                                <h5>Authentication Details:</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Access Token:</strong></td>
                                            <td><code><?php echo htmlspecialchars(substr($accessToken, 0, 20) . '...'); ?></code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Refresh Token:</strong></td>
                                            <td><code><?php echo $refreshToken ? htmlspecialchars(substr($refreshToken, 0, 20) . '...') : 'Not provided'; ?></code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Expires:</strong></td>
                                            <td><?php echo isset($_SESSION['youtube_token_expires']) ? date('Y-m-d H:i:s', $_SESSION['youtube_token_expires']) : 'Unknown'; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="index.php" class="btn btn-success">
                                    Go to Video Page
                                </a>
                                <a href="test-oauth.php?logout=1" class="btn btn-outline-secondary">
                                    Clear Session
                                </a>
                            </div>
                        <?php endif; ?>

                        <hr class="my-4">

                        <div class="mb-3">
                            <h6>Instructions:</h6>
                            <ol>
                                <li>Click "Sign in with Google" to start OAuth2 flow</li>
                                <li>You'll be redirected to Google for authorization</li>
                                <li>After authorization, you'll return here with tokens</li>
                                <li>Use the tokens to access YouTube API</li>
                            </ol>
                        </div>

                        <div class="alert alert-info">
                            <strong>Note:</strong> Make sure the redirect URI in your Google Console matches exactly: 
                            <code><?php echo htmlspecialchars(YOUTUBE_REDIRECT_URI); ?></code>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
