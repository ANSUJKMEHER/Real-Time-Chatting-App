class WebRTCManager {
    constructor() {
        this.peerConnection = null;
        this.localStream = null;
        this.remoteStream = null;
        this.currentCallId = null;
        this.isCaller = false;
        this.ws = null;
        this.pendingOffer = null;
        this.initialize();
    }

    async initialize() {
        try {
            console.log('Initializing WebRTC manager...');
            
            // First check if we can access the devices
            const devices = await navigator.mediaDevices.enumerateDevices();
            console.log('Available devices:', devices);
            
            // Try to get media with specific device IDs if available
            const videoDevices = devices.filter(device => device.kind === 'videoinput');
            const audioDevices = devices.filter(device => device.kind === 'audioinput');
            
            const constraints = {
                audio: audioDevices.length > 0 ? { deviceId: audioDevices[0].deviceId } : true,
                video: videoDevices.length > 0 ? { deviceId: videoDevices[0].deviceId } : true
            };
            
            console.log('Attempting to get media with constraints:', constraints);
            
            this.localStream = await navigator.mediaDevices.getUserMedia(constraints)
                .catch(async error => {
                    console.error('Error accessing media devices:', error);
                    if (error.name === 'NotReadableError') {
                        // Try again with simpler constraints
                        console.log('Retrying with simpler constraints...');
                        return await navigator.mediaDevices.getUserMedia({
                            audio: true,
                            video: false
                        });
                    }
                    throw error;
                });
            
            console.log('Media devices accessed successfully');
            
            const localVideo = document.getElementById('local-video');
            if (localVideo) {
                localVideo.srcObject = this.localStream;
                console.log('Local video stream set');
            } else {
                console.error('Local video element not found');
            }
            
            this.initializeWebSocket();
        } catch (error) {
            console.error('Error in initialize:', error);
            if (error.name === 'NotReadableError') {
                alert('Camera/microphone is in use by another application. Please close other applications using your camera/microphone and refresh the page.');
            } else if (error.name === 'NotAllowedError') {
                alert('Please allow camera and microphone access to use video calling.');
            } else {
                alert('Error accessing camera and microphone: ' + error.message);
            }
        }
    }

    initializeWebSocket() {
        console.log('Initializing WebSocket connection...');
        const protocol = window.location.protocol === 'https:' ? 'wss://' : 'ws://';
        const wsUrl = `${protocol}${window.location.hostname}:8080`;
        console.log('WebSocket URL:', wsUrl);
        
        this.ws = new WebSocket(wsUrl);
        
        this.ws.onopen = () => {
            console.log('WebSocket connected successfully');
            this.ws.send(JSON.stringify({
                type: 'register',
                userId: currentUserId
            }));
            console.log('User registered with ID:', currentUserId);
        };

        this.ws.onmessage = (event) => {
            console.log('WebSocket message received:', event.data);
            const data = JSON.parse(event.data);
            this.handleSignalingData(data);
        };

        this.ws.onerror = (error) => {
            console.error('WebSocket error:', error);
            alert('Connection error. Please make sure the WebSocket server is running.');
        };

        this.ws.onclose = () => {
            console.log('WebSocket connection closed');
            // Try to reconnect after 5 seconds
            setTimeout(() => {
                console.log('Attempting to reconnect...');
                this.initializeWebSocket();
            }, 5000);
        };
    }

    handleSignalingData(data) {
        console.log('Handling signaling data:', data);
        if (!this.peerConnection) {
            console.log('No peer connection, creating new one');
            this.createPeerConnection();
        }

        switch (data.type) {
            case 'offer':
                console.log('Received offer from:', data.from);
                this.handleOffer(data);
                break;
            case 'answer':
                console.log('Received answer from:', data.from);
                this.handleAnswer(data);
                break;
            case 'candidate':
                console.log('Received ICE candidate from:', data.from);
                this.handleCandidate(data);
                break;
            case 'call':
                console.log('Received call request from:', data.from);
                this.handleIncomingCall(data);
                break;
            default:
                console.log('Unknown message type:', data.type);
        }
    }

    async handleOffer(data) {
        try {
            await this.peerConnection.setRemoteDescription(new RTCSessionDescription(data.offer));
            const answer = await this.peerConnection.createAnswer();
            await this.peerConnection.setLocalDescription(answer);
            
            this.ws.send(JSON.stringify({
                type: 'answer',
                to: data.from,
                answer: answer
            }));
        } catch (error) {
            console.error('Error handling offer:', error);
        }
    }

    async handleAnswer(data) {
        try {
            await this.peerConnection.setRemoteDescription(new RTCSessionDescription(data.answer));
        } catch (error) {
            console.error('Error handling answer:', error);
        }
    }

    async handleCandidate(data) {
        try {
            await this.peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate));
        } catch (error) {
            console.error('Error handling ICE candidate:', error);
        }
    }

    handleIncomingCall(data) {
        console.log('Handling incoming call:', data);
        // Store the offer data temporarily
        this.pendingOffer = data.offer;
        
        // Show incoming call UI
        const callDialog = document.createElement('div');
        callDialog.className = 'call-dialog';
        callDialog.innerHTML = `
            <div class="call-content">
                <h3>Incoming ${data.callType} call</h3>
                <div class="call-buttons">
                    <button onclick="webrtcManager.acceptCall('${data.from}')">Accept</button>
                    <button onclick="webrtcManager.rejectCall('${data.from}')">Reject</button>
                </div>
            </div>
        `;
        document.body.appendChild(callDialog);
    }

    createPeerConnection() {
        if (!this.localStream) {
            console.error('Local stream not available');
            return;
        }

        console.log('Creating peer connection...');
        const configuration = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' }
            ]
        };

        this.peerConnection = new RTCPeerConnection(configuration);
        console.log('Peer connection created');

        // Add local stream to peer connection
        this.localStream.getTracks().forEach(track => {
            console.log('Adding track:', track.kind);
            this.peerConnection.addTrack(track, this.localStream);
        });

        // Handle remote stream
        this.peerConnection.ontrack = (event) => {
            console.log('Received remote track');
            this.remoteStream = event.streams[0];
            const remoteVideo = document.getElementById('remote-video');
            if (remoteVideo) {
                remoteVideo.srcObject = this.remoteStream;
            }
        };

        // Handle ICE candidates
        this.peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                console.log('New ICE candidate');
                this.ws.send(JSON.stringify({
                    type: 'candidate',
                    to: selectedUserId,
                    candidate: event.candidate
                }));
            }
        };

        // Handle connection state changes
        this.peerConnection.onconnectionstatechange = () => {
            console.log('Connection state:', this.peerConnection.connectionState);
        };

        // Handle ICE connection state changes
        this.peerConnection.oniceconnectionstatechange = () => {
            console.log('ICE connection state:', this.peerConnection.iceConnectionState);
        };
    }

    async initiateCall(receiverId, callType) {
        console.log('Initiating call to:', receiverId, 'Type:', callType);
        
        if (!this.localStream) {
            console.error('Local stream not available, trying to reinitialize...');
            await this.initialize();
            if (!this.localStream) {
                alert('Could not access camera and microphone. Please check your permissions.');
                return;
            }
        }

        this.isCaller = true;
        this.createPeerConnection();

        try {
            const offer = await this.peerConnection.createOffer();
            await this.peerConnection.setLocalDescription(offer);
            console.log('Created and set local offer');

            const callData = {
                type: 'call',
                to: receiverId,
                from: currentUserId,
                callType: callType,
                offer: offer
            };
            console.log('Sending call data:', callData);
            
            if (this.ws && this.ws.readyState === WebSocket.OPEN) {
                this.ws.send(JSON.stringify(callData));
                console.log('Call request sent successfully');
                this.showCallUI(true);
            } else {
                console.error('WebSocket is not connected');
                alert('Connection error. Please refresh the page.');
            }
        } catch (error) {
            console.error('Error in initiateCall:', error);
            alert('Error initiating call. Please try again.');
        }
    }

    async acceptCall(callerId) {
        console.log('Accepting call from:', callerId);
        this.isCaller = false;
        
        if (!this.pendingOffer) {
            console.error('No pending offer found');
            return;
        }

        this.createPeerConnection();

        try {
            console.log('Setting remote description with offer:', this.pendingOffer);
            await this.peerConnection.setRemoteDescription(new RTCSessionDescription(this.pendingOffer));
            
            console.log('Creating answer');
            const answer = await this.peerConnection.createAnswer();
            
            console.log('Setting local description');
            await this.peerConnection.setLocalDescription(answer);

            console.log('Sending answer');
            this.ws.send(JSON.stringify({
                type: 'answer',
                to: callerId,
                answer: answer
            }));

            this.showCallUI(false);
            // Remove call dialog
            document.querySelector('.call-dialog').remove();
            
            // Clear the pending offer
            this.pendingOffer = null;
        } catch (error) {
            console.error('Error accepting call:', error);
            alert('Error accepting call: ' + error.message);
        }
    }

    async rejectCall(callId) {
        this.ws.send(JSON.stringify({
            type: 'reject',
            to: selectedUserId,
            callId: callId
        }));
        // Remove call dialog
        document.querySelector('.call-dialog').remove();
    }

    async endCall() {
        if (this.peerConnection) {
            this.peerConnection.close();
            this.peerConnection = null;
        }

        this.ws.send(JSON.stringify({
            type: 'end',
            to: selectedUserId
        }));

        this.hideCallUI();
    }

    showCallUI(isCaller) {
        const callControls = document.getElementById('call-controls');
        const videoContainer = document.getElementById('video-container');
        const localVideo = document.getElementById('local-video');
        const remoteVideo = document.getElementById('remote-video');
        const endCallBtn = document.getElementById('end-call-btn');

        if (!callControls || !videoContainer || !localVideo || !remoteVideo || !endCallBtn) {
            console.error('Required UI elements not found');
            return;
        }

        callControls.style.display = 'block';
        videoContainer.style.display = 'block';
        localVideo.style.display = 'block';
        remoteVideo.style.display = 'block';
        
        if (isCaller) {
            endCallBtn.style.display = 'block';
        }
    }

    hideCallUI() {
        const callControls = document.getElementById('call-controls');
        const videoContainer = document.getElementById('video-container');
        const localVideo = document.getElementById('local-video');
        const remoteVideo = document.getElementById('remote-video');
        const endCallBtn = document.getElementById('end-call-btn');

        if (!callControls || !videoContainer || !localVideo || !remoteVideo || !endCallBtn) {
            console.error('Required UI elements not found');
            return;
        }

        callControls.style.display = 'none';
        videoContainer.style.display = 'none';
        localVideo.style.display = 'none';
        remoteVideo.style.display = 'none';
        endCallBtn.style.display = 'none';
    }
}

// Initialize WebRTC manager
const webrtcManager = new WebRTCManager();

// Event listeners for call controls
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, setting up event listeners');
    const startAudioCall = document.getElementById('start-audio-call');
    const startVideoCall = document.getElementById('start-video-call');
    const endCallBtn = document.getElementById('end-call-btn');

    if (startAudioCall) {
        startAudioCall.addEventListener('click', () => {
            console.log('Audio call button clicked');
            webrtcManager.initiateCall(selectedUserId, 'audio');
        });
    }

    if (startVideoCall) {
        startVideoCall.addEventListener('click', () => {
            console.log('Video call button clicked');
            webrtcManager.initiateCall(selectedUserId, 'video');
        });
    }

    if (endCallBtn) {
        endCallBtn.addEventListener('click', () => {
            console.log('End call button clicked');
            webrtcManager.endCall();
        });
    }
}); 