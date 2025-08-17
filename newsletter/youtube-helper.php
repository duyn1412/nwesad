<?php
/**
 * YouTube Helper Class
 * Fetches video information from YouTube using Video ID
 */
class YouTubeHelper {
    private $apiKey;
    private $apiUrl = 'https://www.googleapis.com/youtube/v3/videos';
    
    public function __construct($apiKey = null) {
        $this->apiKey = $apiKey;
    }
    
    /**
     * Set YouTube API Key
     */
    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
    }
    
    /**
     * Get video information from YouTube Video ID
     * 
     * @param string $videoId YouTube Video ID
     * @return array|false Video information or false on error
     */
    public function getVideoInfo($videoId) {
        if (empty($this->apiKey)) {
            throw new Exception('YouTube API Key is required');
        }
        
        if (empty($videoId)) {
            throw new Exception('Video ID is required');
        }
        
        // Build API URL
        $url = $this->apiUrl . '?' . http_build_query([
            'part' => 'snippet,statistics',
            'id' => $videoId,
            'key' => $this->apiKey
        ]);
        
        // Make API request
        $response = $this->makeRequest($url);
        
        if (!$response) {
            return false;
        }
        
        // Parse response
        $data = json_decode($response, true);
        
        if (isset($data['items']) && count($data['items']) > 0) {
            $video = $data['items'][0];
            $snippet = $video['snippet'];
            $statistics = $video['statistics'] ?? [];
            
            return [
                'video_id' => $videoId,
                'title' => $snippet['title'] ?? '',
                'description' => $snippet['description'] ?? '',
                'thumbnail_default' => $snippet['thumbnails']['default']['url'] ?? '',
                'thumbnail_medium' => $snippet['thumbnails']['medium']['url'] ?? '',
                'thumbnail_high' => $snippet['thumbnails']['high']['url'] ?? '',
                'thumbnail_maxres' => $snippet['thumbnails']['maxres']['url'] ?? '',
                'channel_title' => $snippet['channelTitle'] ?? '',
                'published_at' => $snippet['publishedAt'] ?? '',
                'duration' => $snippet['duration'] ?? '',
                'view_count' => $statistics['viewCount'] ?? 0,
                'like_count' => $statistics['likeCount'] ?? 0,
                'comment_count' => $statistics['commentCount'] ?? 0
            ];
        }
        
        return false;
    }
    
    /**
     * Get video thumbnail URL
     * 
     * @param string $videoId YouTube Video ID
     * @param string $quality Thumbnail quality (default, medium, high, maxres)
     * @return string|false Thumbnail URL or false on error
     */
    public function getThumbnail($videoId, $quality = 'medium') {
        $videoInfo = $this->getVideoInfo($videoId);
        
        if (!$videoInfo) {
            return false;
        }
        
        $qualityKey = 'thumbnail_' . $quality;
        return $videoInfo[$qualityKey] ?? false;
    }
    
    /**
     * Get video title
     * 
     * @param string $videoId YouTube Video ID
     * @return string|false Video title or false on error
     */
    public function getTitle($videoId) {
        $videoInfo = $this->getVideoInfo($videoId);
        return $videoInfo ? $videoInfo['title'] : false;
    }
    
    /**
     * Get video description
     * 
     * @param string $videoId YouTube Video ID
     * @return string|false Video description or false on error
     */
    public function getDescription($videoId) {
        $videoInfo = $this->getVideoInfo($videoId);
        return $videoInfo ? $videoInfo['description'] : false;
    }
    
    /**
     * Extract Video ID from YouTube URL
     * 
     * @param string $url YouTube URL
     * @return string|false Video ID or false on error
     */
    public function extractVideoId($url) {
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        
        return false;
    }
    
    /**
     * Validate YouTube Video ID
     * 
     * @param string $videoId Video ID to validate
     * @return bool True if valid, false otherwise
     */
    public function isValidVideoId($videoId) {
        // YouTube Video ID is typically 11 characters
        return preg_match('/^[a-zA-Z0-9_-]{11}$/', $videoId);
    }
    
    /**
     * Make HTTP request
     * 
     * @param string $url URL to request
     * @return string|false Response content or false on error
     */
    private function makeRequest($url) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'NWES-Admin/1.0'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($httpCode === 200 && $response !== false) {
            return $response;
        }
        
        return false;
    }
    
    /**
     * Get embed URL for video
     * 
     * @param string $videoId YouTube Video ID
     * @return string Embed URL
     */
    public function getEmbedUrl($videoId) {
        return "https://www.youtube.com/embed/{$videoId}";
    }
    
    /**
     * Get watch URL for video
     * 
     * @param string $videoId YouTube Video ID
     * @return string Watch URL
     */
    public function getWatchUrl($videoId) {
        return "https://www.youtube.com/watch?v={$videoId}";
    }
}

?>


