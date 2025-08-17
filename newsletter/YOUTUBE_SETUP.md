# YouTube Integration Setup Guide

## Overview
This guide explains how to set up YouTube integration for the newsletter system to automatically fetch video thumbnails, titles, and descriptions.

## Features
- **Automatic Video Info Fetching**: Get video details from YouTube Video ID
- **Thumbnail Display**: Show video thumbnails in different qualities
- **Video Metadata**: Title, description, channel, views, likes, etc.
- **URL Parsing**: Extract Video ID from YouTube URLs
- **Real-time Preview**: See video information before saving

## Setup Steps

### 1. Get YouTube Data API Key

1. **Go to Google Cloud Console**: https://console.developers.google.com/
2. **Create a new project** or select existing one
3. **Enable YouTube Data API v3**:
   - Go to "APIs & Services" > "Library"
   - Search for "YouTube Data API v3"
   - Click "Enable"
4. **Create credentials**:
   - Go to "APIs & Services" > "Credentials"
   - Click "Create Credentials" > "API Key"
   - Copy your API key

### 2. Configure API Key

**Option 1: Environment Variable (Recommended)**
```bash
# Add to your .env file or server environment
YOUTUBE_API_KEY=your_api_key_here
```

**Option 2: Direct in Code**
Edit `youtube-integration.php`:
```php
$youtube->setApiKey('your_api_key_here');
```

### 3. Test the Integration

1. **Run test script**: `test-youtube.php`
2. **Enter a YouTube Video ID** (e.g., `dQw4w9WgXcQ`)
3. **Click "Fetch Info"** to test API connection

## Usage

### In Newsletter Form

1. **Enter Video ID or URL**:
   - Video ID: `dQw4w9WgXcQ`
   - Full URL: `https://www.youtube.com/watch?v=dQw4w9WgXcQ`
   - Short URL: `https://youtu.be/dQw4w9WgXcQ`

2. **Click "Fetch Info"** button

3. **View Video Preview**:
   - Thumbnail image
   - Video title and description
   - Channel information
   - View count and statistics

4. **Save Newsletter** with video information

### Programmatic Usage

```php
// Include YouTube Helper
include 'youtube-helper.php';

// Initialize with API key
$youtube = new YouTubeHelper();
$youtube->setApiKey('YOUR_API_KEY');

// Get video information
$videoInfo = $youtube->getVideoInfo('dQw4w9WgXcQ');

if ($videoInfo) {
    $title = $videoInfo['title'];
    $thumbnail = $videoInfo['thumbnail_medium'];
    $description = $videoInfo['description'];
    $channel = $videoInfo['channel_title'];
    $views = $videoInfo['view_count'];
}
```

## API Response Structure

```json
{
    "success": true,
    "data": {
        "video_id": "dQw4w9WgXcQ",
        "title": "Video Title",
        "description": "Video Description",
        "thumbnail_default": "https://...",
        "thumbnail_medium": "https://...",
        "thumbnail_high": "https://...",
        "thumbnail_maxres": "https://...",
        "channel_title": "Channel Name",
        "published_at": "2024-01-01T00:00:00Z",
        "view_count": "1000000",
        "like_count": "50000",
        "comment_count": "1000"
    },
    "message": "Video information fetched successfully"
}
```

## Supported URL Formats

- `https://www.youtube.com/watch?v=VIDEO_ID`
- `https://youtu.be/VIDEO_ID`
- `https://www.youtube.com/embed/VIDEO_ID`
- `https://www.youtube.com/v/VIDEO_ID`

## Error Handling

### Common Errors

1. **"YouTube API Key is required"**
   - Set your API key in the configuration

2. **"Invalid YouTube Video ID format"**
   - Video ID must be 11 characters
   - Check for extra spaces or invalid characters

3. **"Failed to fetch video information"**
   - Video might be private or deleted
   - Check API key permissions
   - Verify internet connection

### Debug Mode

Enable debug mode in your PHP configuration:
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## Security Considerations

1. **API Key Protection**:
   - Never commit API keys to version control
   - Use environment variables
   - Restrict API key to specific domains

2. **Rate Limiting**:
   - YouTube API has daily quotas
   - Monitor usage in Google Cloud Console

3. **Input Validation**:
   - Always validate Video ID format
   - Sanitize output for XSS protection

## Troubleshooting

### API Quota Exceeded
- Check quota usage in Google Cloud Console
- Consider upgrading to paid tier
- Implement caching for frequently accessed videos

### CORS Issues
- Ensure proper headers in AJAX requests
- Check server configuration

### Performance Issues
- Implement video information caching
- Use appropriate thumbnail quality
- Consider lazy loading for multiple videos

## Support

For technical support:
- Check Google Cloud Console for API issues
- Review YouTube Data API documentation
- Contact development team for code issues

## Updates

- **v1.0**: Initial YouTube integration
- **v1.1**: Added URL parsing and validation
- **v1.2**: Enhanced error handling and preview
