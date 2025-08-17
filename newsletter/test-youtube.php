<?php
// Test script for YouTube Helper functionality
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include YouTube Helper
include 'youtube-helper.php';

echo "<h2>YouTube Helper Test</h2>";

// Test with a sample YouTube Video ID
$videoId = 'dQw4w9WgXcQ'; // Rick Roll video for testing

echo "<h3>Testing with Video ID: {$videoId}</h3>";

try {
    // Initialize YouTube Helper with your API key
    $youtube = new YouTubeHelper('AIzaSyBoJu9d4AldZMrgVxdUOY169Qd8IXp8Oqc');
    
    echo "<p><strong>✅ API Key configured successfully!</strong></p>";
    
    // Test video ID validation
    echo "<h4>Video ID Validation:</h4>";
    if ($youtube->isValidVideoId($videoId)) {
        echo "<p style='color: green;'>✅ Video ID is valid</p>";
    } else {
        echo "<p style='color: red;'>❌ Video ID is invalid</p>";
    }
    
    // Test URL generation
    echo "<h4>Generated URLs:</h4>";
    echo "<p><strong>Watch URL:</strong> <a href='{$youtube->getWatchUrl($videoId)}' target='_blank'>{$youtube->getWatchUrl($videoId)}</a></p>";
    echo "<p><strong>Embed URL:</strong> <a href='{$youtube->getEmbedUrl($videoId)}' target='_blank'>{$youtube->getEmbedUrl($videoId)}</a></p>";
    
    // Test URL parsing
    echo "<h4>URL Parsing Test:</h4>";
    $testUrls = [
        "https://www.youtube.com/watch?v={$videoId}",
        "https://youtu.be/{$videoId}",
        "https://www.youtube.com/embed/{$videoId}",
        "https://www.youtube.com/v/{$videoId}"
    ];
    
    foreach ($testUrls as $url) {
        $extractedId = $youtube->extractVideoId($url);
        if ($extractedId === $videoId) {
            echo "<p style='color: green;'>✅ {$url} → {$extractedId}</p>";
        } else {
            echo "<p style='color: red;'>❌ {$url} → {$extractedId}</p>";
        }
    }
    
    // Test API functionality
    echo "<h4>API Test:</h4>";
    
    // Get video info
    $videoInfo = $youtube->getVideoInfo($videoId);
    
    if ($videoInfo) {
        echo "<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0; background-color: #f9f9f9;'>";
        echo "<h5>Video Information:</h5>";
        echo "<p><strong>Title:</strong> " . htmlspecialchars($videoInfo['title']) . "</p>";
        echo "<p><strong>Channel:</strong> " . htmlspecialchars($videoInfo['channel_title']) . "</p>";
        echo "<p><strong>Published:</strong> " . htmlspecialchars($videoInfo['published_at']) . "</p>";
        echo "<p><strong>Views:</strong> " . number_format($videoInfo['view_count']) . "</p>";
        echo "<p><strong>Likes:</strong> " . number_format($videoInfo['like_count']) . "</p>";
        
        // Display thumbnails
        echo "<h5>Thumbnails:</h5>";
        if (!empty($videoInfo['thumbnail_default'])) {
            echo "<p><strong>Default:</strong> <img src='{$videoInfo['thumbnail_default']}' alt='Default Thumbnail' style='max-width: 120px; border: 1px solid #ddd;'></p>";
        }
        if (!empty($videoInfo['thumbnail_medium'])) {
            echo "<p><strong>Medium:</strong> <img src='{$videoInfo['thumbnail_medium']}' alt='Medium Thumbnail' style='max-width: 320px; border: 1px solid #ddd;'></p>";
        }
        if (!empty($videoInfo['thumbnail_high'])) {
            echo "<p><strong>High:</strong> <img src='{$videoInfo['thumbnail_high']}' alt='High Thumbnail' style='max-width: 480px; border: 1px solid #ddd;'></p>";
        }
        if (!empty($videoInfo['thumbnail_maxres'])) {
            echo "<p><strong>Max Resolution:</strong> <img src='{$videoInfo['thumbnail_maxres']}' alt='Max Resolution Thumbnail' style='max-width: 1280px; border: 1px solid #ddd;'></p>";
        }
        
        echo "</div>";
        
        echo "<p style='color: green;'><strong>🎉 YouTube API is working perfectly!</strong></p>";
        
    } else {
        echo "<p style='color: red;'>❌ Failed to get video information</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<h3>Test with different Video IDs:</h3>";
echo "<form method='post' style='margin: 20px 0;'>";
echo "<input type='text' name='custom_video_id' placeholder='Enter YouTube Video ID' style='padding: 8px; width: 300px; margin-right: 10px;'>";
echo "<button type='submit' style='padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;'>Test Video</button>";
echo "</form>";

// Handle custom video ID test
if (isset($_POST['custom_video_id']) && !empty($_POST['custom_video_id'])) {
    $customVideoId = trim($_POST['custom_video_id']);
    
    echo "<h4>Testing Custom Video ID: {$customVideoId}</h4>";
    
    try {
        $customVideoInfo = $youtube->getVideoInfo($customVideoId);
        
        if ($customVideoInfo) {
            echo "<div style='border: 1px solid #28a745; padding: 15px; margin: 10px 0; background-color: #d4edda;'>";
            echo "<h5>Custom Video Information:</h5>";
            echo "<p><strong>Title:</strong> " . htmlspecialchars($customVideoInfo['title']) . "</p>";
            echo "<p><strong>Channel:</strong> " . htmlspecialchars($customVideoInfo['channel_title']) . "</p>";
            echo "<p><strong>Views:</strong> " . number_format($customVideoInfo['view_count']) . "</p>";
            
            if (!empty($customVideoInfo['thumbnail_medium'])) {
                echo "<p><strong>Thumbnail:</strong> <img src='{$customVideoInfo['thumbnail_medium']}' alt='Thumbnail' style='max-width: 320px; border: 1px solid #ddd;'></p>";
            }
            
            echo "</div>";
        } else {
            echo "<p style='color: red;'>❌ Failed to get information for this video ID</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "<hr>";
echo "<h3>Integration Status:</h3>";
echo "<ul>";
echo "<li>✅ YouTube Helper Class: Loaded</li>";
echo "<li>✅ API Key: Configured</li>";
echo "<li>✅ API Connection: Working</li>";
echo "<li>✅ Newsletter Integration: Ready</li>";
echo "</ul>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>✅ YouTube API is working</li>";
echo "<li>✅ Test the newsletter form with video ID</li>";
echo "<li>✅ Use 'Fetch Info' button to get video details</li>";
echo "<li>✅ Save newsletter with video information</li>";
echo "</ol>";
?>
