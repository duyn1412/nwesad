<?php
/**
 * Simple OAuth Test Page - No OAuth Required
 */

session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple OAuth Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Server Test - OAuth Configuration</h4>
                    </div>
                    <div class="card-body">
                        
                        <div class="alert alert-success">
                            <strong>✅ Server is working!</strong> 
                            This page loaded successfully without OAuth configuration.
                        </div>

                        <div class="mb-4">
                            <h5>Current Status:</h5>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    PHP Version
                                    <span class="badge bg-success rounded-pill">
                                        <?php echo PHP_VERSION; ?>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Server Software
                                    <span class="badge bg-info rounded-pill">
                                        <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Current Directory
                                    <span class="badge bg-secondary rounded-pill">
                                        <?php echo __DIR__; ?>
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <h5>Required Files Check:</h5>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    oauth-config.php
                                    <span class="badge bg-<?php echo file_exists(__DIR__ . '/oauth-config.php') ? 'success' : 'danger'; ?> rounded-pill">
                                        <?php echo file_exists(__DIR__ . '/oauth-config.php') ? 'Exists' : 'Missing'; ?>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    credentials.php
                                    <span class="badge bg-<?php echo file_exists(__DIR__ . '/credentials.php') ? 'success' : 'danger'; ?> rounded-pill">
                                        <?php echo file_exists(__DIR__ . '/credentials.php') ? 'Exists' : 'Missing'; ?>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    nav-admin.php (parent directory)
                                    <span class="badge bg-<?php echo file_exists(__DIR__ . '/../nav-admin.php') ? 'success' : 'danger'; ?> rounded-pill">
                                        <?php echo file_exists(__DIR__ . '/../nav-admin.php') ? 'Exists' : 'Missing'; ?>
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <div class="alert alert-warning">
                            <strong>⚠️ To fix OAuth errors:</strong>
                            <ol class="mb-0 mt-2">
                                <li>Create <code>video/credentials.php</code> with your OAuth credentials</li>
                                <li>Create <code>video/oauth-config.php</code> from the template</li>
                                <li>Ensure <code>nav-admin.php</code> exists in the parent directory</li>
                            </ol>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="index.php" class="btn btn-primary">
                                Go to Video Page
                            </a>
                            <a href="../index.php" class="btn btn-outline-secondary">
                                Go to Main Page
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
