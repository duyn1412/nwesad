<?php
/**
 * YouTube Integration for Newsletter
 * Automatically fetches video information when VIDEO_ID is entered
 */

// Include YouTube Helper
include 'youtube-helper.php';

// Initialize YouTube Helper with your API key
$youtube = new YouTubeHelper('AIzaSyBoJu9d4AldZMrgVxdUOY169Qd8IXp8Oqc');

/**
 * Process video information and return formatted data
 */
function processVideoInfo($videoId, $youtubeHelper) {
    if (empty($videoId)) {
        return [
            'success' => false,
            'message' => 'Video ID is required'
        ];
    }
    
    try {
        // Validate video ID
        if (!$youtubeHelper->isValidVideoId($videoId)) {
            return [
                'success' => false,
                'message' => 'Invalid YouTube Video ID format'
            ];
        }
        
        // Get video information
        $videoInfo = $youtubeHelper->getVideoInfo($videoId);
        
        if (!$videoInfo) {
            return [
                'success' => false,
                'message' => 'Failed to fetch video information'
            ];
        }
        
        return [
            'success' => true,
            'data' => $videoInfo,
            'message' => 'Video information fetched successfully'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

/**
 * Generate HTML for video preview
 */
function generateVideoPreview($videoInfo) {
    $html = '<div class="video-preview" style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px;">';
    $html .= '<h4>Video Preview</h4>';
    
    // Thumbnail
    if (!empty($videoInfo['thumbnail_medium'])) {
        $html .= '<div style="text-align: center; margin-bottom: 15px;">';
        $html .= '<img src="' . htmlspecialchars($videoInfo['thumbnail_medium']) . '" alt="Video Thumbnail" style="max-width: 100%; height: auto; border-radius: 5px;">';
        $html .= '</div>';
    }
    
    // Video information
    $html .= '<div class="video-info">';
    $html .= '<p><strong>Title:</strong> ' . htmlspecialchars($videoInfo['title']) . '</p>';
    $html .= '<p><strong>Channel:</strong> ' . htmlspecialchars($videoInfo['channel_title']) . '</p>';
    $html .= '<p><strong>Published:</strong> ' . htmlspecialchars($videoInfo['published_at']) . '</p>';
    $html .= '<p><strong>Views:</strong> ' . number_format($videoInfo['view_count']) . '</p>';
    
    // Duration (if available)
    if (!empty($videoInfo['duration'])) {
        $html .= '<p><strong>Duration:</strong> ' . htmlspecialchars($videoInfo['duration']) . '</p>';
    }
    
    // Links
    $html .= '<div style="margin-top: 15px;">';
    $html .= '<a href="' . htmlspecialchars($videoInfo['watch_url'] ?? 'https://www.youtube.com/watch?v=' . $videoInfo['video_id']) . '" target="_blank" class="btn btn-primary btn-sm">Watch on YouTube</a> ';
    $html .= '<a href="' . htmlspecialchars($videoInfo['embed_url'] ?? 'https://www.youtube.com/embed/' . $videoInfo['video_id']) . '" target="_blank" class="btn btn-secondary btn-sm">Embed URL</a>';
    $html .= '</div>';
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Generate newsletter HTML with video
 */
function generateNewsletterWithVideo($videoInfo, $newsletterData) {
    $html = '<div class="newsletter-content">';
    
    // Header
    if (!empty($newsletterData['TOP_HEADER_TXT'])) {
        $html .= '<h1>' . htmlspecialchars($newsletterData['TOP_HEADER_TXT']) . '</h1>';
    }
    
    if (!empty($newsletterData['TOP_TEXT_TXT'])) {
        $html .= '<p>' . htmlspecialchars($newsletterData['TOP_TEXT_TXT']) . '</p>';
    }
    
    // Video section
    if (!empty($videoInfo)) {
        $html .= '<div class="video-section" style="margin: 20px 0; text-align: center;">';
        $html .= '<h3>Featured Video</h3>';
        
        // Video thumbnail with link
        if (!empty($videoInfo['thumbnail_medium'])) {
            $html .= '<a href="https://www.youtube.com/watch?v=' . htmlspecialchars($videoInfo['video_id']) . '" target="_blank">';
            $html .= '<img src="' . htmlspecialchars($videoInfo['thumbnail_medium']) . '" alt="' . htmlspecialchars($videoInfo['title']) . '" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">';
            $html .= '</a>';
        }
        
        // Video title and description
        $html .= '<h4>' . htmlspecialchars($videoInfo['title']) . '</h4>';
        if (!empty($videoInfo['description'])) {
            $html .= '<p>' . htmlspecialchars(substr($videoInfo['description'], 0, 200)) . (strlen($videoInfo['description']) > 200 ? '...' : '') . '</p>';
        }
        
        // Watch button
        $html .= '<a href="https://www.youtube.com/watch?v=' . htmlspecialchars($videoInfo['video_id']) . '" target="_blank" style="display: inline-block; background: #ff0000; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;">Watch on YouTube</a>';
        
        $html .= '</div>';
    }
    
    // Links section
    if (!empty($newsletterData['LINK_URL_1']) || !empty($newsletterData['LINK_URL_2']) || !empty($newsletterData['LINK_URL_3'])) {
        $html .= '<div class="links-section" style="margin: 20px 0;">';
        $html .= '<h3>Related Links</h3>';
        
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($newsletterData["LINK_URL_{$i}"])) {
                $html .= '<div style="margin: 15px 0; padding: 15px; border: 1px solid #eee; border-radius: 5px;">';
                $html .= '<h4><a href="' . htmlspecialchars($newsletterData["LINK_URL_{$i}"]) . '" target="_blank">' . htmlspecialchars($newsletterData["LINK_OG_TITLE_{$i}"] ?? "Link {$i}") . '</a></h4>';
                if (!empty($newsletterData["LINK_TEXT_{$i}"])) {
                    $html .= '<p>' . htmlspecialchars($newsletterData["LINK_TEXT_{$i}"]) . '</p>';
                }
                if (!empty($newsletterData["LINK_OG_IMG_{$i}"])) {
                    $html .= '<img src="' . htmlspecialchars($newsletterData["LINK_OG_IMG_{$i}"]) . '" alt="Link Image" style="max-width: 200px; height: auto;">';
                }
                $html .= '</div>';
            }
        }
        
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

// Handle AJAX request for video info
if (isset($_POST['action']) && $_POST['action'] === 'get_video_info') {
    $videoId = $_POST['video_id'] ?? '';
    
    $result = processVideoInfo($videoId, $youtube);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>