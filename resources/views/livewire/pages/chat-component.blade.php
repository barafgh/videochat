<div x-data="chat" x-init="initChat">
    <div class="relative flex flex-col-reverse h-full sm:h-[calc(100vh_-_150px)] sm:flex-row gap-5"
         :class="{'min-h-[999px]' : isShowChatMenu}">
        <div
            class="panel absolute z-10 hidden w-full sm:max-w-xs flex-none space-y-4 overflow-hidden p-4 xl:relative xl:block xl:h-full"
            :class="isShowChatMenu && '!block'">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-none">
                        <img src="{{$authUser['path']}}" class="h-12 w-12 rounded-full object-cover"/>
                    </div>
                    <div class="mx-3">
                        <p class="mb-1 font-semibold">{{$authUser['name']}}</p>
                        <p class="text-xs text-white-dark">Software Developer</p>
                    </div>
                </div>
            </div>
            <div class="relative">
                <input
                    type="text"
                    class="peer form-input ltr:pr-9 rtl:pl-9"
                    placeholder="Search..."
                    x-model="searchUser"
                    @keyup="searchUsers"
                />
            </div>
            <div class="h-px w-full border-b border-[#e0e6ed]"></div>
            <div class="!mt-0">
                <div
                    class="chat-users perfect-scrollbar relative -mr-3.5 h-full min-h-[100px] space-y-0.5 pr-3.5 sm:h-[calc(100vh_-_357px)]">
                    <template x-for="person in searchUsers">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between rounded-md p-2 hover:bg-gray-100 hover:text-primary"
                            :class="{'bg-gray-100 text-primary': selectedUser.userId === person.userId}"
                            wire:click="loadMessages(person.roomId)"
                            @click="selectUser(person);">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <div class="relative flex-shrink-0">
                                        <img :src="person.path"
                                             class="h-12 w-12 rounded-full object-cover"/>
                                        <template x-if="person.active">
                                            <div class="absolute bottom-0 ltr:right-0 rtl:left-0">
                                                <div class="h-4 w-4 rounded-full bg-success"></div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="mx-3 ltr:text-left rtl:text-right">
                                        <p class="mb-1 font-semibold" x-text="person.name"></p>
                                        <p class="max-w-[185px] truncate text-xs text-white-dark"
                                           x-text="person.preview"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="whitespace-nowrap text-xs font-semibold">
                                <p x-text="person.time"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </div>
        <div
            class="absolute z-[5] hidden h-full w-full rounded-md bg-black/60"
            :class="isShowChatMenu && '!block xl:!hidden'"
            @click="isShowChatMenu = !isShowChatMenu">
        </div>
        <div class="panel flex-1 p-0">
            <template x-if="!isShowUserChat">
                <div class="relative flex h-full items-center justify-center p-4">
                    <button
                        type="button"
                        class="absolute top-4 hover:text-primary ltr:left-4 rtl:right-4 xl:hidden"
                        @click="isShowChatMenu = !isShowChatMenu">
                        <i class="fa fa-bars"></i>
                    </button>
                    <div class="flex flex-col items-center justify-center py-8">
                        <button
                            @click="selectUser(searchUsers[0]);"
                            class="mx-auto flex max-w-[190px] justify-center rounded-md bg-white-dark/20 p-2 font-semibold">
                            Click User To Chat
                        </button>
                    </div>
                </div>
            </template>
            <template x-if="isShowUserChat && selectedUser">
                <div class="relative h-full">
                    <div class="flex items-center justify-between p-4">
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <button type="button" class="hover:text-primary xl:hidden"
                                    @click="isShowChatMenu = !isShowChatMenu">
                                <i class="fa fa-arrow-left"></i>
                            </button>
                            <div class="relative flex-none">
                                <img
                                    :src="selectedUser.path"
                                    class="h-10 w-10 rounded-full object-cover sm:h-12 sm:w-12"
                                />
                                <div class="absolute bottom-0 ltr:right-0 rtl:left-0">
                                    <div class="h-4 w-4 rounded-full bg-success"></div>
                                </div>
                            </div>
                            <div class="mx-3">
                                <p class="font-semibold" x-text="selectedUser.name"></p>
                                <p
                                    class="text-xs text-white-dark"
                                    x-text="selectedUser.active ? 'Active now' : 'Last seen at '+selectedUser.time"
                                ></p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" @click="startCall(false)"
                                    class="bg-blue-500 text-white px-3 py-2 rounded-full hover:bg-blue-600">
                                <i class="fa-solid fa-phone"></i>
                            </button>
                            <button type="button" @click="startCall(true)"
                                    class="bg-green-500 text-white px-3 py-2 rounded-full hover:bg-green-600">
                                <i class="fa-solid fa-video"></i>
                            </button>
                        </div>
                    </div>
                    <div class="h-px w-full border-b border-[#e0e6ed]"></div>
                    <div class="perfect-scrollbar relative h-full overflow-auto sm:h-[calc(100vh_-_300px)]">
                        <div
                            class="chat-conversation-box min-h-[400px] space-y-5 p-4 pb-[68px] sm:min-h-[300px] sm:pb-0">
                            <template x-for="message in @this.messages">
                                <div class="flex items-start gap-3"
                                     :class="{'justify-end' : selectedUser.userId === message.fromUserId}">
                                    <div class="flex-none"
                                         :class="{'order-2' : selectedUser.userId === message.fromUserId}">
                                        <template x-if="selectedUser.userId === message.fromUserId">
                                            <img :src="loginUser.path" class="h-10 w-10 rounded-full object-cover"/>
                                        </template>
                                        <template x-if="selectedUser.userId !== message.fromUserId">
                                            <img :src="selectedUser.path"
                                                 class="h-10 w-10 rounded-full object-cover"/>
                                        </template>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="rounded-md bg-black/10 p-4 py-2"
                                                :class="message.fromUserId == selectedUser.userId ? 'ltr:rounded-br-none rtl:rounded-bl-none !bg-primary text-white' : 'ltr:rounded-bl-none rtl:rounded-br-none'"
                                                x-text="message.text"
                                            ></div>
                                            <div :class="{'hidden' : selectedUser.userId === message.fromUserId}">
                                            </div>
                                        </div>
                                        <div
                                            class="text-xs text-white-dark"
                                            :class="{'ltr:text-right rtl:text-left' : selectedUser.userId === message.fromUserId}"
                                            x-text="message.time ? message.time: '5h ago'"
                                        ></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 w-full p-4 bg-white">
                        <div class="w-full items-center space-x-3 rtl:space-x-reverse sm:flex">
                            <div class="relative flex-1">
                                <input
                                    id=""
                                    class="form-input rounded-full border-0 bg-[#f4f4f4] px-12 py-2 focus:outline-none"
                                    placeholder="Type a message"
                                    x-model="textMessage"
                                    wire:model="message"
                                    wire:keydown.enter="sendMessage(selectedUser.userId)"
                                    @keyup.enter="sendMessage();"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <div x-show="isReceivingCall" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75">
        <div class="bg-white p-4 rounded-md text-center">
            <h2 class="text-lg font-semibold">Incoming Call</h2>
            <img :src="callerDetails.path" class="h-16 w-16 rounded-full object-cover mx-auto mb-4"/>
            <p class="text-lg font-semibold" x-text="callerDetails.name"></p>
            <div class="mt-4 flex justify-center space-x-4">
                <button @click="answerCall" class="bg-green-500 text-white px-4 py-2 rounded">Answer</button>
                <button @click="rejectCall" class="bg-red-500 text-white px-4 py-2 rounded">Reject</button>
            </div>
        </div>
    </div>
    <div x-show="isInCall" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75">
        <div class="bg-white p-4 rounded-md">
            <h2 class="text-lg font-semibold">Call in Progress</h2>
            <p x-text="callStatus"></p>
            <p x-show="callTimer" x-text="'Duration: ' + callTimer + ' seconds'"></p>
            <video id="localVideo" autoplay muted class="w-64 h-48" x-show="isVideoCall"></video>
            <video id="remoteVideo" autoplay class="w-64 h-48" x-show="isVideoCall"></video>
            <div class="mt-4">
                <button @click="endCall" class="bg-red-500 text-white px-4 py-2 rounded">End Call</button>
            </div>
        </div>
    </div>
    <div x-show="callSummary" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75">
        <div class="bg-white p-4 rounded-md text-center">
            <h2 class="text-lg font-semibold">Call Summary</h2>
            <p x-text="callSummary ? 'Call with ' + callSummary.name : ''"></p>
            <p x-text="callSummary ? 'Duration: ' + callSummary.duration + ' seconds' : ''"></p>
            <button @click="closeSummary" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Close</button>
        </div>
    </div>
    <div x-show="callAnswered" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75">
        <div class="bg-white p-4 rounded-md text-center">
            <h2 class="text-lg font-semibold">Call in Progress</h2>
            <p class="text-md font-medium">Talking to <span x-text="selectedUser.name"></span></p>
            <p x-show="callTimer" class="text-sm" x-text="'Duration: ' + callTimer + ' seconds'"></p>
            <video id="localVideo" autoplay muted class="w-64 h-48" x-show="isVideoCall"></video>
            <video id="remoteVideo" autoplay class="w-64 h-48" x-show="isVideoCall"></video>
            <div class="mt-4">
                <button @click="endCall" class="bg-red-500 text-white px-4 py-2 rounded">End Call</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chat', () => ({
                isShowUserChat: false,
                isShowChatMenu: false,
                isReceivingCall: false,
                isInCall: false,
                callSummary: null,
                callTimer: null,
                callStartTime: null,
                callInterval: null,
                callStatus: '',
                isVideoCall: false,
                loginUser:
                    @json($authUser),
                contactList: @json($contacts),
                searchUser: '',
                textMessage: '',
                selectedUser: '',
                callerDetails: {},
                localStream: null,
                remoteStream: null,
                peerConnection: null,
                iceServers: [
                    {urls: 'stun:stun.l.google.com:19302'},
                    {urls: 'stun:stun1.l.google.com:19302'},
                    {urls: 'stun:stun2.l.google.com:19302'},
                    {urls: 'stun:stun3.l.google.com:19302'},
                    {urls: 'stun:stun4.l.google.com:19302'},
                ],
                incomingCallType: 'video',
                handlingCall: false,
                callAnswered: false,
                answerSDPSet: false,
                pendingIceCandidates: [],

                initChat() {
                    Livewire.on('refreshContacts', (updatedContacts) => {
                        this.contactList = updatedContacts;
                        this.$nextTick(() => {
                            this.scrollToBottom;
                        });
                    }),
                        Livewire.on('call-received', (event) => {
                            this.handleIncomingSignal(event[0]);
                        });
                },

                get searchUsers() {
                    return this.contactList.filter((user) => {
                        return user.name.toLowerCase().includes(this.searchUser.toLowerCase());
                    });
                },

                selectUser(user) {
                    this.selectedUser = user;
                    this.isShowUserChat = true;
                    this.scrollToBottom;
                    this.isShowChatMenu = false;
                },

                sendMessage() {
                    if (this.textMessage.trim()) {
                        this.textMessage = '';
                        this.scrollToBottom;
                    }
                },

                startCall(isVideo = true) {
                    this.isVideoCall = isVideo;
                    this.isInCall = true;
                    const mediaConstraints = {
                        video: isVideo,
                        audio: true
                    };
                    if (!this.handlingCall) {
                        this.handlingCall = true;
                        console.log('Starting call with constraints:', mediaConstraints);
                        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                            navigator.mediaDevices.getUserMedia(mediaConstraints)
                                .then(stream => {
                                    this.localStream = stream;
                                    document.getElementById('localVideo').srcObject = stream;
                                    this.createPeerConnection();
                                    this.localStream.getTracks().forEach(track => {
                                        console.log('Adding track:', track.label);
                                        this.peerConnection.addTrack(track, this.localStream);
                                    });

                                    this.peerConnection.createOffer()
                                        .then(offer => {
                                            return this.peerConnection.setLocalDescription(offer);
                                        })
                                        .then(() => {
                                            console.log('Local description set:', this.peerConnection.localDescription);
                                        @this.sendSignal(this.selectedUser.userId, {sdp: this.peerConnection.localDescription})
                                            ;
                                        });
                                })
                                .catch(error => {
                                    console.error('Error starting call:', error);
                                    this.callStatus = 'Failed to start the call. Please check your camera and microphone permissions.';
                                });
                        } else {
                            console.error('navigator.mediaDevices.getUserMedia is not supported in this browser.');
                            this.callStatus = 'Your browser does not support video calls.';
                        }
                    }
                },

                createPeerConnection() {
                    console.log('Creating new RTCPeerConnection');
                    this.peerConnection = new RTCPeerConnection({iceServers: this.iceServers});

                    this.peerConnection.oniceconnectionstatechange = () => {
                        console.log('ICE connection state:', this.peerConnection.iceConnectionState);
                        if (this.peerConnection.iceConnectionState === 'failed') {
                            this.callStatus = 'Call failed due to network issues.';
                        }
                    };

                    this.peerConnection.onconnectionstatechange = () => {
                        console.log('Connection state:', this.peerConnection.connectionState);
                        if (this.peerConnection.connectionState === 'disconnected') {
                            this.callStatus = 'Call disconnected.';
                        }
                    };

                    this.peerConnection.onicecandidate = event => {
                        if (event.candidate) {
                        @this.sendSignal(this.selectedUser.userId, {candidate: event.candidate})
                            ;
                        }
                    };

                    this.peerConnection.ontrack = event => {
                        console.log('Received remote track:', event.streams[0]);
                        this.remoteStream = event.streams[0];
                        document.getElementById('remoteVideo').srcObject = this.remoteStream;
                    };
                },

                handleIncomingSignal(event) {
                    const signal = event.callData.signal;
                    if (!this.peerConnection) {
                        this.createPeerConnection();
                    }

                    if (signal.sdp) {
                        console.log('Handling sdp:', signal.sdp);
                        const remoteDesc = new RTCSessionDescription(signal.sdp);
                        if (remoteDesc.type === 'answer' && !this.answerSDPSet) {
                            if (this.peerConnection.signalingState === 'have-local-offer') {
                                if (this.peerConnection.signalingState !== 'stable') {
                                    this.peerConnection.setRemoteDescription(remoteDesc)
                                        .then(() => {
                                            console.log('Remote answer SDP set successfully.');
                                            this.answerSDPSet = true;
                                        })
                                        .catch(error => {
                                            console.error('Error setting remote answer SDP:', this.peerConnection.signalingState);
                                        });
                                }

                            } else {
                                console.warn('Unexpected signaling state:', this.peerConnection.signalingState);
                            }
                        } else if (remoteDesc.type === 'offer') {
                            this.isReceivingCall = true;
                            this.isVideoCall = signal.sdp.sdp.includes('m=video');
                            this.callerDetails = event.callData.caller;
                            this.incomingCallType = event.callData.isVideo ? 'video' : 'audio';
                            if (this.peerConnection.signalingState === 'stable' || this.peerConnection.signalingState === 'have-remote-offer') {
                                this.peerConnection.setRemoteDescription(remoteDesc)
                                    .catch(error => {
                                        console.error('Error handling remote offer SDP:', error);
                                    });
                            } else {
                                console.warn('Unexpected signaling state:', this.peerConnection.signalingState);
                            }
                        }
                    } else if (signal.candidate) {
                        if (this.peerConnection.remoteDescription && this.peerConnection.remoteDescription.type) {
                            this.peerConnection.addIceCandidate(new RTCIceCandidate(signal.candidate))
                                .then(() => {
                                    console.log('ICE candidate added successfully:', signal.candidate);
                                })
                                .catch(error => {
                                    console.error('Error adding received ICE candidate:', error);
                                });
                        } else {
                            console.warn('Remote description not set yet. Saving ICE candidate.');
                            this.pendingIceCandidates.push(new RTCIceCandidate(signal.candidate));
                        }
                    }
                },

                answerCall() {
                    if (this.callAnswered) {
                        return;
                    }
                    console.log('Answering call');
                    this.isReceivingCall = false;
                    this.callAnswered = true;
                    this.callStatus = 'Connected';
                    this.callStartTime = Date.now();
                    this.callInterval = setInterval(() => {
                        this.callTimer = Math.floor((Date.now() - this.callStartTime) / 1000);
                    }, 1000);

                    const mediaConstraints = this.incomingCallType === 'video'
                        ? {video: true, audio: true}
                        : {video: false, audio: true};

                    navigator.mediaDevices.getUserMedia(mediaConstraints)
                        .then(stream => {
                            this.localStream = stream;
                            document.getElementById('localVideo').srcObject = stream;

                            this.localStream.getTracks().forEach(track => {
                                console.log('Adding track:', track);
                                this.peerConnection.addTrack(track, this.localStream);
                            });

                            if (this.peerConnection.remoteDescription && this.peerConnection.remoteDescription.type === 'offer') {
                                console.log('Creating answer');
                                return this.peerConnection.createAnswer()
                                    .then(answer => {
                                        console.log('Answer created:', answer);
                                        return this.peerConnection.setLocalDescription(answer);
                                    })
                                    .then(() => {
                                        console.log('Local description set:', this.peerConnection.localDescription);
                                    @this.sendSignal(this.callerDetails.id, {sdp: this.peerConnection.localDescription})
                                        ;
                                    })
                                    .catch(error => {
                                        console.error('Error creating or setting answer:', error);
                                    });
                            } else {
                                console.error('Remote description is not set to "offer" state');
                            }
                        })
                        .catch(error => {
                            console.error('Error answering call:', error);
                        });
                },


                rejectCall() {
                    console.log('Rejecting call');
                    this.isReceivingCall = false;
                    this.showCallSummary('rejected');
                },

                endCall() {
                    console.log('Ending call');
                    this.showCallSummary('ended');
                },

                showCallSummary(status) {
                    console.log('Showing call summary. Status:', status);
                    clearInterval(this.callInterval);
                    this.isInCall = false;
                    this.callSummary = {
                        name: this.selectedUser.name,
                        duration: this.callTimer,
                        status: status
                    };
                @this.saveCallSummary(this.callSummary, this.selectedUser.userId)
                    ;
                },

                closeSummary() {
                    this.callSummary = null;
                    this.callTimer = null;
                    this.callAnswered = false;
                },

                get scrollToBottom() {
                    if (this.isShowUserChat) {
                        setTimeout(() => {
                            const element = document.querySelector('.chat-conversation-box');
                            element.scrollIntoView({
                                behavior: 'smooth',
                                block: 'end',
                            });
                        });
                    }
                },
            }));
        });
    </script>
@endpush
