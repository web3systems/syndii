/**
 * Live Transcription with OpenAI's gpt-4o-transcribe model
 * 
 * This script handles the recording, processing, and displaying of live transcriptions
 * using the browser's MediaRecorder API and OpenAI's transcription service.
 */

class LiveTranscription {
    constructor(options = {}) {
        // Configuration
        this.options = {
            chunkSize: options.chunkSize || 5000, // Size of audio chunks in ms
            language: options.language || 'en',
            autoStart: options.autoStart || false,
            continuous: options.continuous || true,
            processingEndpoint: options.processingEndpoint || '/app/user/speech-text-pro/live',
            onTranscriptionUpdate: options.onTranscriptionUpdate || function(text) {},
            onStatusChange: options.onStatusChange || function(status) {},
            onError: options.onError || function(error) {}
        };

        // State
        this.mediaRecorder = null;
        this.audioChunks = [];
        this.isRecording = false;
        this.stream = null;
        this.startTime = null;
        this.transcript = '';
        this.processingQueue = [];
        this.isProcessing = false;
    }

    /**
     * Initialize the transcription service
     */
    async init() {
        try {
            // Check if getUserMedia is supported
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Your browser does not support audio recording. Please try a modern browser like Chrome, Firefox, or Edge.');
            }
            
            // Request microphone access with specific constraints for better compatibility
            this.stream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true
                }
            });
            
            // Verify we have a valid MediaStream
            if (!(this.stream instanceof MediaStream)) {
                throw new Error('Failed to get a valid MediaStream from microphone');
            }
            
            // Check if MediaRecorder is supported
            if (typeof MediaRecorder === 'undefined') {
                throw new Error('Your browser does not support MediaRecorder. Please try a modern browser.');
            }
            
            this.options.onStatusChange('ready');
            
            if (this.options.autoStart) {
                this.start();
            }
            
            return true;
        } catch (error) {
            this.options.onError('Microphone access denied: ' + error.message);
            console.error('Initialization error:', error);
            return false;
        }
    }

    /**
     * Start recording
     */
    async start() {
        if (this.isRecording) return;
        
        try {
            // Check if we have a valid stream, if not, try to initialize it
            if (!this.stream) {
                const initialized = await this.init();
                if (!initialized) {
                    return false;
                }
            }
            
            // Make sure we have a valid MediaStream object
            if (!(this.stream instanceof MediaStream)) {
                throw new Error('Invalid MediaStream object');
            }
            
            // Try to use a MIME type that OpenAI supports directly
            let mimeType = '';
            
            // Check for supported formats in order of preference
            if (MediaRecorder.isTypeSupported('audio/mp3')) {
                mimeType = 'audio/mp3';
            } else if (MediaRecorder.isTypeSupported('audio/mpeg')) {
                mimeType = 'audio/mpeg';
            } else if (MediaRecorder.isTypeSupported('audio/wav')) {
                mimeType = 'audio/wav';
            } else if (MediaRecorder.isTypeSupported('audio/m4a')) {
                mimeType = 'audio/m4a';
            } else if (MediaRecorder.isTypeSupported('audio/mp4')) {
                mimeType = 'audio/mp4';
            } else if (MediaRecorder.isTypeSupported('audio/webm')) {
                mimeType = 'audio/webm';
            } else if (MediaRecorder.isTypeSupported('audio/ogg')) {
                mimeType = 'audio/ogg';
            }
            
            console.log('Using MIME type:', mimeType || 'default');
            
            // Set recorder options
            const options = mimeType ? { mimeType } : {};
            
            // Add audio quality options if possible
            if (mimeType) {
                try {
                    options.audioBitsPerSecond = 128000; // 128 kbps
                } catch (e) {
                    console.warn('Could not set audioBitsPerSecond:', e);
                }
            }
            
            this.mediaRecorder = new MediaRecorder(this.stream, options);
            this.audioChunks = [];
            
            // Set up event handlers
            this.mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    this.audioChunks.push(event.data);
                    
                    // If we're in continuous mode, process chunks as they come
                    if (this.options.continuous && this.audioChunks.length > 0) {
                        this.processCurrentChunks();
                    }
                }
            };
            
            // Start recording
            this.mediaRecorder.start(this.options.chunkSize);
            this.isRecording = true;
            this.startTime = Date.now();
            this.options.onStatusChange('recording');
            
            return true;
        } catch (error) {
            this.options.onError('Failed to start recording: ' + error.message);
            console.error('MediaRecorder error:', error);
            return false;
        }
    }

    /**
     * Stop recording
     */
    async stop() {
        if (!this.isRecording) return;
        
        return new Promise((resolve) => {
            this.mediaRecorder.onstop = async () => {
                // Process any remaining audio
                if (this.audioChunks.length > 0) {
                    await this.processCurrentChunks();
                }
                
                this.isRecording = false;
                this.options.onStatusChange('stopped');
                resolve();
            };
            
            this.mediaRecorder.stop();
        });
    }

    /**
     * Process current audio chunks
     */
    async processCurrentChunks() {
        if (this.audioChunks.length === 0) return;
        
        // Create a copy of the current chunks and clear the original array
        const chunksToProcess = [...this.audioChunks];
        this.audioChunks = [];
        
        // Add to processing queue
        this.processingQueue.push(chunksToProcess);
        
        // Start processing if not already in progress
        if (!this.isProcessing) {
            this.processQueue();
        }
    }
    
    /**
     * Convert audio blob to MP3 format if possible using MediaRecorder
     * This is a fallback method that may not work in all browsers
     */
    async tryConvertToMP3(audioBlob) {
        // If we already have an MP3, no need to convert
        if (audioBlob.type === 'audio/mp3' || audioBlob.type === 'audio/mpeg') {
            return audioBlob;
        }
        
        try {
            // Create an audio element to play the blob
            const audioElement = new Audio();
            const objectUrl = URL.createObjectURL(audioBlob);
            audioElement.src = objectUrl;
            
            // Create an audio context
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) {
                console.warn('AudioContext not supported, cannot convert audio format');
                return audioBlob;
            }
            
            const audioContext = new AudioContext();
            const source = audioContext.createMediaElementSource(audioElement);
            const destination = audioContext.createMediaStreamDestination();
            source.connect(destination);
            
            // Play the audio (may be silent)
            await audioElement.play();
            
            // Create a new MediaRecorder with mp3 mime type if supported
            let mp3Recorder;
            if (MediaRecorder.isTypeSupported('audio/mp3')) {
                mp3Recorder = new MediaRecorder(destination.stream, { mimeType: 'audio/mp3' });
            } else if (MediaRecorder.isTypeSupported('audio/mpeg')) {
                mp3Recorder = new MediaRecorder(destination.stream, { mimeType: 'audio/mpeg' });
            } else {
                console.warn('MP3 format not supported by MediaRecorder');
                URL.revokeObjectURL(objectUrl);
                return audioBlob;
            }
            
            // Record the audio
            const mp3Chunks = [];
            mp3Recorder.ondataavailable = e => mp3Chunks.push(e.data);
            
            mp3Recorder.start();
            
            // Wait for the audio to finish playing
            await new Promise(resolve => {
                audioElement.onended = resolve;
                // Set a timeout in case the audio doesn't trigger onended
                setTimeout(resolve, audioElement.duration * 1000 || 3000);
            });
            
            // Stop recording
            mp3Recorder.stop();
            
            // Wait for the last chunk
            await new Promise(resolve => {
                mp3Recorder.onstop = resolve;
            });
            
            // Clean up
            URL.revokeObjectURL(objectUrl);
            
            // Create a new blob with MP3 format
            return new Blob(mp3Chunks, { type: 'audio/mp3' });
        } catch (error) {
            console.error('Error converting audio format:', error);
            return audioBlob; // Return original blob if conversion fails
        }
    }

    /**
     * Process the queue of audio chunks
     */
    async processQueue() {
        if (this.processingQueue.length === 0) {
            this.isProcessing = false;
            return;
        }
        
        this.isProcessing = true;
        this.options.onStatusChange('processing');
        
        const chunksToProcess = this.processingQueue.shift();
        
        try {
            // Determine the best MIME type to use
            // Try to use MP3 first if supported, as OpenAI prefers it
            const mimeType = MediaRecorder.isTypeSupported('audio/mp3') 
                ? 'audio/mp3' 
                : MediaRecorder.isTypeSupported('audio/mpeg') 
                    ? 'audio/mpeg'
                    : MediaRecorder.isTypeSupported('audio/wav') 
                        ? 'audio/wav'
                        : MediaRecorder.isTypeSupported('audio/webm') 
                            ? 'audio/webm' 
                            : MediaRecorder.isTypeSupported('audio/mp4') 
                                ? 'audio/mp4' 
                                : 'audio/ogg';
            
            // Create a blob from the audio chunks
            let audioBlob = new Blob(chunksToProcess, { type: mimeType });
            
            // Check if the blob has content
            if (audioBlob.size === 0) {
                console.warn('Empty audio blob, skipping processing');
                this.isProcessing = false;
                
                // Process next item in queue if any
                if (this.processingQueue.length > 0) {
                    this.processQueue();
                }
                return;
            }
            
            // If not already in a format OpenAI supports, try to convert
            const supportedFormats = ['audio/mp3', 'audio/mpeg', 'audio/wav', 'audio/m4a', 'audio/mp4'];
            if (!supportedFormats.includes(audioBlob.type)) {
                console.log('Attempting to convert audio to a supported format...');
                try {
                    // Try to convert to MP3 format
                    const convertedBlob = await this.tryConvertToMP3(audioBlob);
                    if (convertedBlob.type !== audioBlob.type) {
                        console.log('Successfully converted audio to', convertedBlob.type);
                        audioBlob = convertedBlob;
                    }
                } catch (conversionError) {
                    console.warn('Audio conversion failed:', conversionError);
                    // Continue with original format and let the server handle it
                }
            }
            
            // Convert blob to base64
            const base64Audio = await this.blobToBase64(audioBlob);
            
            // Send to server for transcription
            const response = await this.sendToServer(base64Audio);
            
            if (response && response.status === 200 && response.text) {
                // Update transcript
                if (this.transcript.length === 0) {
                    this.transcript = response.text;
                } else {
                    this.transcript += ' ' + response.text;
                }
                
                // Notify listeners
                this.options.onTranscriptionUpdate(this.transcript);
            } else if (response && response.status !== 200) {
                console.warn('Server returned non-200 status:', response);
                if (response.message) {
                    this.options.onError(response.message);
                }
            }
        } catch (error) {
            this.options.onError('Transcription error: ' + error.message);
            console.error('Process queue error:', error);
        } finally {
            this.isProcessing = false;
            
            // Process next item in queue if any
            if (this.processingQueue.length > 0) {
                this.processQueue();
            }
        }
    }

    /**
     * Convert Blob to Base64
     */
    blobToBase64(blob) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onloadend = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(blob);
        });
    }

    /**
     * Send audio data to server for processing
     */
    async sendToServer(audioData) {
        try {
            // Check if we have valid audio data
            if (!audioData || audioData === 'data:') {
                throw new Error('Invalid audio data');
            }
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                throw new Error('CSRF token not found');
            }
            
            const response = await fetch(this.options.processingEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    audio_data: audioData,
                    language: this.options.language,
                    save_transcript: false
                })
            });
            
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Server returned ${response.status}: ${errorText}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('Server request error:', error);
            throw new Error('Server request failed: ' + error.message);
        }
    }

    /**
     * Get the current transcript
     */
    getTranscript() {
        return this.transcript;
    }

    /**
     * Clear the current transcript
     */
    clearTranscript() {
        this.transcript = '';
        this.options.onTranscriptionUpdate('');
        return true;
    }

    /**
     * Get recording duration in seconds
     */
    getRecordingDuration() {
        if (!this.startTime) return 0;
        return Math.floor((Date.now() - this.startTime) / 1000);
    }

    /**
     * Save the current transcript
     */
    async saveTranscript() {
        if (!this.transcript || this.transcript.trim() === '') {
            this.options.onError('No transcript to save');
            return false;
        }
        
        try {
            const response = await fetch(this.options.processingEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    text: this.transcript,
                    save_transcript: true,
                    duration: this.getRecordingDuration()
                })
            });
            
            const result = await response.json();
            return result.status === 200;
        } catch (error) {
            this.options.onError('Failed to save transcript: ' + error.message);
            return false;
        }
    }

    /**
     * Clean up resources
     */
    destroy() {
        if (this.isRecording) {
            this.stop();
        }
        
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
        }
        
        this.mediaRecorder = null;
        this.stream = null;
        this.audioChunks = [];
        this.processingQueue = [];
    }
}