/**
 * YouTube Transcript Fetcher Application
 * Handles YouTube URL input, transcript fetching, and OpenAI Assistant integration
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const youtubeUrlInput = document.getElementById('youtubeUrl');
    const fetchBtn = document.getElementById('fetchBtn');
    const transcriptArea = document.getElementById('transcript');
    const sendBtn = document.getElementById('sendBtn');
    const assistantReplyArea = document.getElementById('assistantReply');
    const transcriptStatus = document.getElementById('transcriptStatus');
    const assistantStatus = document.getElementById('assistantStatus');

    // Check if elements exist before adding event listeners
    if (fetchBtn) {
        fetchBtn.addEventListener('click', handleFetchTranscript);
    }

    if (sendBtn) {
        sendBtn.addEventListener('click', handleSendToAssistant);
    }

    if (youtubeUrlInput) {
        youtubeUrlInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                handleFetchTranscript();
            }
        });
    }

    /**
     * Handle transcript fetching from YouTube
     */
    async function handleFetchTranscript() {
        if (!youtubeUrlInput || !fetchBtn || !transcriptArea || !transcriptStatus) {
            console.error('Required DOM elements not found');
            return;
        }

        const youtubeUrl = youtubeUrlInput.value.trim();
        
        if (!youtubeUrl) {
            showError(transcriptStatus, 'Please enter a YouTube URL');
            return;
        }

        // Validate YouTube URL
        if (!isValidYouTubeUrl(youtubeUrl)) {
            showError(transcriptStatus, 'Please enter a valid YouTube URL');
            return;
        }

        // Show loading state
        setLoadingState(fetchBtn, true);
        transcriptStatus.textContent = 'Fetching transcript...';
        transcriptStatus.className = 'text-info';

        try {
            const response = await fetch('fetch_transcript.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ youtube_url: youtubeUrl })
            });

            const data = await response.json();

            if (data.error) {
                // Handle error
                showError(transcriptStatus, data.error);
                transcriptArea.value = '';
                sendBtn.disabled = true;
            } else if (data.success) {
                // Handle success
                if (data.available_captions && data.available_captions.length > 0) {
                    transcriptArea.value = `Video ID: ${data.video_id}\n\nAvailable Captions:\n${formatCaptions(data.available_captions)}\n\nTranscript Preview:\n${data.transcript_preview}\n\nTotal Lines: ${data.total_lines}\n\n${data.message}`;
                    sendBtn.disabled = false;
                    showSuccess(transcriptStatus, 'Transcript retrieved successfully');
                } else {
                    transcriptArea.value = `No captions available for this video.\n\n${data.message}`;
                    sendBtn.disabled = true;
                    showWarning(transcriptStatus, 'No captions available');
                }
            } else {
                // Handle unexpected response
                showError(transcriptStatus, 'Unexpected response format');
                transcriptArea.value = '';
                sendBtn.disabled = true;
            }

        } catch (error) {
            console.error('Error fetching transcript:', error);
            showError(transcriptStatus, 'Failed to fetch transcript. Please try again.');
            transcriptArea.value = '';
            sendBtn.disabled = true;
        } finally {
            setLoadingState(fetchBtn, false);
        }
    }

    /**
     * Handle sending transcript to OpenAI Assistant
     */
    async function handleSendToAssistant() {
        if (!sendBtn || !assistantReplyArea || !assistantStatus) {
            console.error('Required DOM elements not found');
            return;
        }

        const transcript = transcriptArea.value.trim();
        
        if (!transcript) {
            showError(assistantStatus, 'No transcript to send');
            return;
        }

        // Show loading state
        setLoadingState(sendBtn, true);
        assistantStatus.textContent = 'Sending to Assistant...';
        assistantStatus.className = 'text-info';

        try {
            const response = await fetch('openai_assistant.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    transcript: transcript,
                    action: 'process_transcript'
                })
            });

            const data = await response.json();

            if (data.success) {
                assistantReplyArea.value = data.response;
                showSuccess(assistantStatus, 'Assistant response received successfully');
            } else {
                showError(assistantStatus, data.error || 'Failed to get assistant response');
                assistantReplyArea.value = '';
            }

        } catch (error) {
            console.error('Error sending to assistant:', error);
            showError(assistantStatus, 'Failed to send to assistant. Please try again.');
            assistantReplyArea.value = '';
        } finally {
            setLoadingState(sendBtn, false);
        }
    }

    /**
     * Display video information
     */
    function displayVideoInfo(data) {
        // You can add video preview display here if needed
        console.log('Video loaded:', data.title);
    }

    /**
     * Format captions information
     */
    function formatCaptions(captions) {
        return captions.map((caption, index) => {
            const flags = [];
            if (caption.isCC) flags.push('CC');
            if (caption.isAutoGenerated) flags.push('Auto');
            if (caption.isDraft) flags.push('Draft');
            
            return `${index + 1}. ${caption.language} (${caption.trackKind})${flags.length > 0 ? ' [' + flags.join(', ') + ']' : ''}`;
        }).join('\n');
    }

    /**
     * Validate YouTube URL
     */
    function isValidYouTubeUrl(url) {
        const patterns = [
            /youtube\.com\/watch\?v=[a-zA-Z0-9_-]+/,
            /youtu\.be\/[a-zA-Z0-9_-]+/,
            /youtube\.com\/embed\/[a-zA-Z0-9_-]+/,
            /youtube\.com\/v\/[a-zA-Z0-9_-]+/
        ];
        
        return patterns.some(pattern => pattern.test(url));
    }

    /**
     * Set loading state for buttons
     */
    function setLoadingState(button, isLoading) {
        if (!button) return;
        
        const btnText = button.querySelector('.btn-text');
        const spinner = button.querySelector('.spinner-border');
        
        if (btnText) btnText.textContent = isLoading ? 'Loading...' : button.dataset.originalText || 'Button';
        if (spinner) spinner.classList.toggle('d-none', !isLoading);
        button.disabled = isLoading;
    }

    /**
     * Show success message
     */
    function showSuccess(element, message) {
        if (!element) return;
        element.textContent = message;
        element.className = 'text-success';
    }

    /**
     * Show error message
     */
    function showError(element, message) {
        if (!element) return;
        element.textContent = message;
        element.className = 'text-danger';
    }

    /**
     * Show warning message
     */
    function showWarning(element, message) {
        if (!element) return;
        element.textContent = message;
        element.className = 'text-warning';
    }

    // Store original button text for loading states
    if (fetchBtn) {
        fetchBtn.dataset.originalText = fetchBtn.querySelector('.btn-text')?.textContent || 'Fetch Transcript';
    }
    
    if (sendBtn) {
        sendBtn.dataset.originalText = sendBtn.querySelector('.btn-text')?.textContent || 'Send to Assistant';
    }
});

