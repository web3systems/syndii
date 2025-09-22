const config = document.getElementById('app-config');
const instructions = document.getElementById('app-prompt');
const voice = document.getElementById('app-voice');
const model = document.getElementById('app-model');
const GET_VOICE = voice.dataset.voice;
const GET_INSTRUCTIONS = instructions.dataset.prompt;
const GET_EPHEMERAL_KEY_ENDPOINT = config.dataset.ephemeralUrl;
const OPENAI_REALTIME_URL = 'https://api.openai.com/v1/realtime';
const GET_MODEL = model.dataset.model; 
let ephemeralKey = null;
let ephemeralKeyExpiresAt = null;
let peerConnection = null;
let dataChannel = null;
let mediaStream = null;
let isMicActive = false;
this.sidebarElement = document.querySelector('.chat-message-container')?.parentElement;
const micButton = document.getElementById('live_mic_button');
let responseText = '';
let userText = '';
let inputTokens = 0;
let outputTokens = 0;
let new_chat_id = '';


$('#live_mic_button').on('click', function() {
    if (isMicActive) {
        stopMic();        
    } else {      
        startMic();
    }
})

async function fetchEphemeralKey() {
    try {
        const response = await fetch(GET_EPHEMERAL_KEY_ENDPOINT, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error(`Failed to fetch ephemeral key: ${response.status}`);
        }

        const data = await response.json();
        ephemeralKey = data.ephemeral_key;
        ephemeralKeyExpiresAt = Math.floor(new Date(data.expires_at).getTime() / 1000);
    } catch (error) {
        throw error;
    }
}


function isEphemeralKeyExpired() {
    if (!ephemeralKeyExpiresAt) return true;
    const currentTime = Math.floor(Date.now() / 1000);
    return currentTime >= ephemeralKeyExpiresAt;
}


async function setupWebRTC() {
    if (isEphemeralKeyExpired() || !ephemeralKey) {
        await fetchEphemeralKey();
    }

    if (!ephemeralKey) {
        throw new Error('Ephemeral key is missing after fetch attempt');
    }

    peerConnection = new RTCPeerConnection({
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' }, 
        ],
    });

    peerConnection.ontrack = (event) => {
        const audioElement = document.createElement('audio');
        audioElement.srcObject = event.streams[0];
        audioElement.autoplay = true;
        audioElement.controls = true; 
        audioElement.style.display = 'none';
        document.body.appendChild(audioElement);
    };

    peerConnection.onconnectionstatechange = () => {
        if (peerConnection.connectionState === 'failed') {
            console.error('WebRTC connection failed');
        }
    };

    peerConnection.oniceconnectionstatechange = () => {
    };

    dataChannel = peerConnection.createDataChannel('oai-events');
    dataChannel.onopen = () => {
        configureDataChannel();
    };
    dataChannel.onmessage = handleDataChannelMessage;
    dataChannel.onerror = (error) => console.error('Data channel error:', error);

    try {
        mediaStream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaStream.getTracks().forEach((track) => {
            peerConnection.addTrack(track, mediaStream);
        });
    } catch (error) {
        console.error('Failed to capture microphone:', error);
        throw error;
    }

    const offer = await peerConnection.createOffer();
    await peerConnection.setLocalDescription(offer);

    try {
        const response = await fetch(`${OPENAI_REALTIME_URL}?model=${GET_MODEL}`, {
            method: 'POST',
            body: offer.sdp,
            headers: {
                'Authorization': `Bearer ${ephemeralKey}`,
                'Content-Type': 'application/sdp',
            },
        });

        if (!response.ok) {
            const errorData = await response.json();
            console.error('Error posting offer:', errorData);
            if (errorData.error?.code === 'invalid_client_secret') {
                await fetchEphemeralKey();
                const retryResponse = await fetch(`${OPENAI_REALTIME_URL}?model=${GET_MODEL}`, {
                    method: 'POST',
                    body: offer.sdp,
                    headers: {
                        'Authorization': `Bearer ${ephemeralKey}`,
                        'Content-Type': 'application/sdp',
                    },
                });
                if (!retryResponse.ok) {
                    throw new Error(`Failed to send offer after retry: ${retryResponse.status}`);
                }
                const answer = await retryResponse.text();
                await peerConnection.setRemoteDescription({ sdp: answer, type: 'answer' });
            } else {
                throw new Error(`Failed to send offer: ${response.status} - ${errorData.message}`);
            }
        } else {
            const answer = await response.text();
            await peerConnection.setRemoteDescription({ sdp: answer, type: 'answer' });
        }

    } catch (error) {
        console.error('Error establishing WebRTC connection:', error);
        throw error;
    }
}


function configureDataChannel() {
    const event = {
        type: 'session.update',
        session: {
            modalities: ['text', 'audio'],
            input_audio_transcription: {
                "model": "whisper-1"
            }, 
            instructions: GET_INSTRUCTIONS,
            voice: GET_VOICE,
            model: GET_MODEL,
        },
    };
    dataChannel.send(JSON.stringify(event));
}



function handleDataChannelMessage(event) {
    const message = JSON.parse(event.data);

    if (message.type === 'response.audio_transcript.done') {
        responseText = message.transcript;
        if(responseText.length !== 0) {
            let code = makeid(10);
            appendMessage(bot_avatar, "left", responseText, code);
            storeUserMessage(responseText, 'assistant') 
        }

    } else if (message.type === 'conversation.item.input_audio_transcription.completed') {
        userText = message.transcript;
        if(userText.length !== 0) {
            appendMessage(user_avatar, "right", userText, '', uploaded_image);
            storeUserMessage(userText, 'user') 
        }
        
    } else if (message.type === 'response.done') {
        if (message.response.usage.input_tokens) {
            inputTokens = message.response.usage.input_tokens;
            outputTokens = message.response.usage.output_tokens;
            storeUserMessage('', 'tokens') 
        }        
    }
}


function storeUserMessage(message, type) {
    let formData = new FormData();
		formData.append('message', message);
		formData.append('conversation_id', active_id);
		formData.append('model', GET_MODEL);
        formData.append('type', type);
        
        if(type == 'assistant') {
            formData.append('chat_id', new_chat_id);
        } else if (type == 'tokens') {
            formData.append('chat_id', new_chat_id);
            formData.append('input_tokens', inputTokens);
            formData.append('output_tokens', outputTokens);
        }

    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        method: 'post',
        url: '/app/user/chat/storeRealtime',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            if (type == 'user') {
                new_chat_id = data.chat_id;    
            }
            
        },
        error: function(data) {
        }
    })
}


function updateButtonStates() {

    if (isMicActive) {
        $("#live_mic_button").find('i').addClass('fa-signal-stream-slash').removeClass('fa-signal-stream');
        $("#live_mic_button").addClass('is-streaming');
    } else {
        $("#live_mic_button").find('i').addClass('fa-signal-stream').removeClass('fa-signal-stream-slash');
        $("#live_mic_button").removeClass('is-streaming');
    }
}



async function startMic() {
    if (this.sidebarElement) {
        this.sidebarElement.classList.add('recording-active');
    }
    if (isMicActive) return;
    try {
        await setupWebRTC();
        isMicActive = true;
        updateButtonStates();
    } catch (error) {
        console.error('Failed to start microphone:', error);
        alert('Failed to start microphone. Check console for details.');
    }
}


function stopMic() {
    if (this.sidebarElement) {
        this.sidebarElement.classList.remove('recording-active');
    }
    if (!isMicActive) return;
    if (mediaStream) {
        mediaStream.getTracks().forEach(track => track.stop());
        mediaStream = null;
    }
    if (peerConnection) {
        peerConnection.close();
        peerConnection = null;
    }
    if (dataChannel) {
        dataChannel.close();
        dataChannel = null;
    }
    isMicActive = false;
    updateButtonStates();
}



window.addEventListener('beforeunload', () => {
    if (isMicActive) {
        stopMic();
    }
});