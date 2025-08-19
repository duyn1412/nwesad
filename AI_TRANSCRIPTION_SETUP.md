# AI Transcription Setup Guide

## Overview
This guide explains how to set up AI-based transcription using OpenAI Whisper API as a fallback when YouTube Transcript API fails.

## Prerequisites

### 1. OpenAI API Key
- Sign up at [OpenAI](https://platform.openai.com/)
- Get your API key from the dashboard
- Add to `credentials.php`:

```php
<?php
// ... existing credentials ...
define('OPENAI_API_KEY', 'your-openai-api-key-here');
?>
```

### 2. Server Requirements
- **yt-dlp** or **youtube-dl** installed on server
- **PHP** with cURL extension
- **Temporary directory** write permissions

## Installation

### Step 1: Install yt-dlp (Recommended)
```bash
# On Ubuntu/Debian
sudo apt update
sudo apt install yt-dlp

# On CentOS/RHEL
sudo yum install yt-dlp

# On macOS
brew install yt-dlp
```

### Step 2: Install youtube-dl (Fallback)
```bash
# On Ubuntu/Debian
sudo apt install youtube-dl

# On CentOS/RHEL
sudo yum install youtube-dl

# On macOS
brew install youtube-dl
```

### Step 3: Test Installation
```bash
# Test yt-dlp
yt-dlp --version

# Test youtube-dl
youtube-dl --version
```

## Configuration

### 1. Add OpenAI API Key
Edit `credentials.php` in the root directory:

```php
<?php
// Existing YouTube OAuth credentials
define('YOUTUBE_CLIENT_ID', 'your-client-id');
define('YOUTUBE_CLIENT_SECRET', 'your-client-secret');
define('YOUTUBE_REDIRECT_URI', 'your-redirect-uri');

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'your-database');
define('DB_USER', 'your-username');
define('DB_PASS', 'your-password');

// OpenAI API Key for AI transcription
define('OPENAI_API_KEY', 'sk-your-openai-api-key-here');
?>
```

### 2. Set Environment Variable (Alternative)
```bash
export OPENAI_API_KEY="sk-your-openai-api-key-here"
```

## Usage

### Automatic Fallback
The system will automatically use AI transcription when:
1. YouTube Transcript API returns 500 error
2. All alternative endpoints fail
3. OpenAI API key is configured

### Manual Testing
Test AI transcription directly:

```bash
curl -X POST https://your-domain.com/nwesadmin/video/fetch_transcript_ai.php \
  -H "Content-Type: application/json" \
  -d '{"youtube_url": "https://www.youtube.com/watch?v=VIDEO_ID"}'
```

## Cost Estimation

### OpenAI Whisper API Pricing
- **$0.006 per minute** of audio
- **$0.36 per hour** of video
- **$3.60 per 10-hour video**

### Example Costs
| Video Length | Cost |
|-------------|------|
| 5 minutes   | $0.03 |
| 30 minutes  | $0.18 |
| 1 hour      | $0.36 |
| 10 hours    | $3.60 |

## Troubleshooting

### Common Issues

#### 1. "yt-dlp not found"
```bash
# Install yt-dlp
sudo apt install yt-dlp
# or
pip install yt-dlp
```

#### 2. "OpenAI API key not found"
- Check `credentials.php` has `OPENAI_API_KEY`
- Verify API key is valid
- Check environment variables

#### 3. "Failed to download audio"
- Check server has internet access
- Verify yt-dlp/youtube-dl is installed
- Check temporary directory permissions

#### 4. "OpenAI API request failed"
- Verify API key is correct
- Check OpenAI account has credits
- Verify audio file size (max 25MB)

### Debug Information
The system provides detailed debug info in the response:
```json
{
  "debug_info": {
    "youtube_transcript_api_http_code": 500,
    "ai_transcription_http_code": 200,
    "method": "ai_transcription"
  }
}
```

## Security Considerations

### 1. API Key Protection
- Never commit API keys to Git
- Use environment variables when possible
- Rotate API keys regularly

### 2. File Cleanup
- Audio files are automatically deleted after processing
- Temporary files are cleaned up

### 3. Rate Limiting
- OpenAI has rate limits (3 requests per minute for free tier)
- Consider implementing request queuing for high volume

## Performance Optimization

### 1. Caching
Consider implementing transcript caching:
```php
// Check cache first
$cacheKey = 'transcript_' . $videoId;
$cachedTranscript = getFromCache($cacheKey);
if ($cachedTranscript) {
    return $cachedTranscript;
}
```

### 2. Audio Quality
- Use lower audio quality for faster processing
- Consider video length limits

### 3. Parallel Processing
- Process multiple videos in background
- Use job queues for long videos

## Support

For issues with:
- **YouTube API**: Check OAuth2 configuration
- **OpenAI API**: Verify API key and credits
- **yt-dlp**: Check installation and internet access
- **Server**: Check PHP and cURL configuration
