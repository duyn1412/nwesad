# OpenAI Whisper API Setup

## Quick Setup

### 1. Get OpenAI API Key
1. Go to [OpenAI Platform](https://platform.openai.com/)
2. Sign up/Login
3. Go to "API Keys" section
4. Create new API key
5. Copy the key (starts with `sk-`)

### 2. Add to credentials.php
Edit your `credentials.php` file and add:

```php
<?php
// Existing credentials...
define('YOUTUBE_CLIENT_ID', 'your-client-id');
define('YOUTUBE_CLIENT_SECRET', 'your-client-secret');
define('YOUTUBE_REDIRECT_URI', 'your-redirect-uri');

// Database credentials...
define('DB_HOST', 'localhost');
define('DB_NAME', 'your-database');
define('DB_USER', 'your-username');
define('DB_PASS', 'your-password');

// OpenAI API Key for AI transcription
define('OPENAI_API_KEY', 'sk-your-openai-api-key-here');
?>
```

### 3. Install yt-dlp on Server
```bash
# Ubuntu/Debian
sudo apt update && sudo apt install yt-dlp

# CentOS/RHEL
sudo yum install yt-dlp

# macOS
brew install yt-dlp
```

### 4. Test Installation
```bash
yt-dlp --version
```

## Usage

### Automatic Transcription
The system will now use OpenAI Whisper API for all video transcriptions:

1. **Paste YouTube URL** in the form
2. **Click "Fetch Transcript"**
3. **AI will transcribe** the entire video
4. **Get high-quality transcript** with timestamps

### Cost
- **$0.006 per minute** of video
- **$0.36 per hour** of video
- **$3.60 per 10-hour** video

## Troubleshooting

### "OpenAI API key not found"
- Check `credentials.php` has `OPENAI_API_KEY`
- Verify API key starts with `sk-`
- Check file permissions

### "Failed to download audio"
- Install yt-dlp: `sudo apt install yt-dlp`
- Check server internet access
- Verify temporary directory permissions

### "OpenAI API request failed"
- Check API key is valid
- Verify OpenAI account has credits
- Check audio file size (max 25MB)

## Benefits

✅ **100% Coverage** - Works with any YouTube video  
✅ **High Quality** - AI-powered transcription  
✅ **Timestamps** - Word-level timing  
✅ **Multiple Languages** - Automatic language detection  
✅ **No Restrictions** - No YouTube API limits  

## Example Output

```json
{
  "success": true,
  "video_title": "What are PCBs?",
  "transcript_preview": "[00:15] What are PCBs? [00:18] PCBs are printed circuit boards...",
  "method": "ai_transcription",
  "language": "en"
}
```
