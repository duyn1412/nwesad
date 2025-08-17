<?php
/**
 * Test video ID extraction function
 */
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Video ID Extraction</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test { margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; }
        input[type="text"] { width: 400px; padding: 5px; margin: 5px; }
        button { padding: 10px 20px; margin: 5px; }
    </style>
</head>
<body>
    <h1>Test Video ID Extraction Function</h1>
    
    <div class="test info">
        <h3>Test Video ID Extraction</h3>
        <form method="POST">
            <input type="text" name="youtube_url" placeholder="Enter YouTube URL here..." 
                   value="<?php echo htmlspecialchars($_POST['youtube_url'] ?? ''); ?>">
            <button type="submit">Test Extraction</button>
        </form>
    </div>
    
    <?php
    if ($_POST && !empty($_POST['youtube_url'])) {
        $testUrl = $_POST['youtube_url'];
        
        echo '<div class="test info">';
        echo '<h3>Test Results</h3>';
        echo '<strong>Input URL:</strong> ' . htmlspecialchars($testUrl) . '<br>';
        echo '<strong>URL length:</strong> ' . strlen($testUrl) . ' characters<br>';
        echo '<strong>Trimmed URL:</strong> ' . htmlspecialchars(trim($testUrl)) . '<br>';
        
        // Test the exact function from fetch_transcript.php
        function getYouTubeVideoId(string $url): string
        {
            // Clean the URL first
            $url = trim($url);
            
            // Remove any query parameters that might interfere
            $url = preg_replace('/[?&].*$/', '', $url);
            
            echo '<strong>After cleaning:</strong> ' . htmlspecialchars($url) . '<br>';
            
            $patterns = [
                // Short form: youtu.be/VIDEO_ID
                '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
                
                // Standard watch form: youtube.com/watch?v=VIDEO_ID
                '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
                
                // Alternative watch form: youtube.com/watch?feature=...&v=VIDEO_ID
                '/youtube\.com\/watch\?.*[&?]v=([a-zA-Z0-9_-]{11})/',
                
                // Embed form: youtube.com/embed/VIDEO_ID
                '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
                
                // Channel video form: youtube.com/channel/.../videos/VIDEO_ID
                '/youtube\.com\/channel\/[^\/]+\/videos\/([a-zA-Z0-9_-]{11})/',
                
                // User video form: youtube.com/user/.../videos/VIDEO_ID
                '/youtube\.com\/user\/[^\/]+\/videos\/([a-zA-Z0-9_-]{11})/',
                
                // Direct video form: youtube.com/v/VIDEO_ID
                '/youtube\.com\/v\/([a-zA-Z0-9_-]{11})/',
                
                // Mobile form: m.youtube.com/watch?v=VIDEO_ID
                '/m\.youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
                
                // Music form: music.youtube.com/watch?v=VIDEO_ID
                '/music\.youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
                
                // Just the video ID itself (if user pastes only the ID)
                '/^([a-zA-Z0-9_-]{11})$/'
            ];
            
            echo '<strong>Testing patterns:</strong><br>';
            foreach ($patterns as $index => $pattern) {
                echo 'Pattern ' . ($index + 1) . ': ' . htmlspecialchars($pattern) . '<br>';
                if (preg_match($pattern, $url, $matches)) {
                    echo '<span class="success">✅ MATCH FOUND!</span><br>';
                    echo '<strong>Video ID:</strong> ' . htmlspecialchars($matches[1]) . '<br>';
                    return $matches[1];
                } else {
                    echo '❌ No match<br>';
                }
            }
            
            echo '<span class="error">❌ No patterns matched</span><br>';
            return '';
        }
        
        $videoId = getYouTubeVideoId($testUrl);
        
        if ($videoId !== '') {
            echo '<div class="test success">';
            echo '<h4>✅ SUCCESS!</h4>';
            echo '<strong>Extracted Video ID:</strong> ' . htmlspecialchars($videoId) . '<br>';
            echo '<strong>Video ID length:</strong> ' . strlen($videoId) . ' characters<br>';
            echo '<strong>Is valid format:</strong> ' . (preg_match('/^[a-zA-Z0-9_-]{11}$/', $videoId) ? 'YES' : 'NO') . '<br>';
            echo '</div>';
        } else {
            echo '<div class="test error">';
            echo '<h4>❌ FAILED!</h4>';
            echo '<strong>Could not extract video ID</strong><br>';
            echo '<strong>All patterns tested but none matched</strong><br>';
            echo '</div>';
        }
        
        echo '</div>';
    }
    ?>
    
    <div class="test info">
        <h3>Test URLs to try:</h3>
        <ul>
            <li><code>https://www.youtube.com/watch?v=dQw4w9WgXcQ</code></li>
            <li><code>https://youtu.be/dQw4w9WgXcQ</code></li>
            <li><code>https://www.youtube.com/embed/dQw4w9WgXcQ</code></li>
            <li><code>dQw4w9WgXcQ</code> (just the ID)</li>
        </ul>
    </div>
</body>
</html>
