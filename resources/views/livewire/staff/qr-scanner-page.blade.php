<x-support-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">QR Scanner</h2>
                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Dashboard
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if($isScanning)
                    <div class="text-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Scan Beneficiary QR Code</h3>
                        <p class="mt-1 text-sm text-gray-500">Position the QR code in the scanner area</p>
                    </div>
                    <div class="flex justify-center">
                        <div class="w-full max-w-md">
                            <div id="scanner-container" class="relative bg-gray-100 rounded-lg overflow-hidden" style="height: 350px;">
                                <div id="scanner" class="w-full h-full"></div>
                                <div class="absolute inset-0 border-2 border-blue-500 border-dashed pointer-events-none"></div>
                            </div>
                        </div>
                    </div>
                @elseif($showCapture)
                    <div class="text-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Capture Proof of Claim</h3>
                        <p class="mt-1 text-sm text-gray-500">Take a photo of the beneficiary with their claimed item</p>
                    </div>
                    <div class="flex justify-center">
                        <div class="w-full max-w-md">
                            <div id="camera-container" class="relative bg-gray-100 rounded-lg overflow-hidden" style="height: 350px;">
                                <video id="camera" class="w-full h-full object-cover" autoplay playsinline></video>
                                <canvas id="canvas" class="hidden"></canvas>
                                <div class="absolute inset-0 border-2 border-blue-500 border-dashed pointer-events-none"></div>
                            </div>
                            <div class="mt-4 flex justify-center space-x-4">
                                <button id="capture-btn" type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Capture Photo
                                </button>
                                <button wire:click="skip" type="button" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                                    Skip
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Beneficiary Details</h3>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Name</p>
                                <p class="text-lg font-semibold">{{ $beneficiary->first_name }} {{ $beneficiary->middle_name }} {{ $beneficiary->last_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">RSBSA Number</p>
                                <p class="text-lg font-semibold">{{ $beneficiary->rsbsa_no }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Contact</p>
                                <p class="text-lg font-semibold">{{ $beneficiary->contact_num ?: 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Address</p>
                                <p class="text-lg font-semibold">{{ $beneficiary->farmer_address ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <h4 class="text-md font-semibold mb-2">Benefit Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Crop</p>
                                <p class="text-lg font-semibold">{{ $beneficiary->cropsToReceive->crop->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">QR Code</p>
                                <p class="text-lg font-semibold">{{ $beneficiary->cropsToReceive->unique_code }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Distribution</p>
                                <p class="text-lg font-semibold">{{ $beneficiary->barangayDistribution->distribution->title }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Status</p>
                                <p class="text-lg font-semibold">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $beneficiary->cropsToReceive->is_claimed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $beneficiary->cropsToReceive->is_claimed ? 'Claimed' : 'Unclaimed' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center mt-6 space-x-4">
                        {{ $this->confirmQrAction() }}
                        <button wire:click="resetScan" type="button" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Scan Another
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            let scanner;
            let cameraStream = null;

            function initScanner() {
                const scannerContainer = document.getElementById('scanner');
                if (!scannerContainer) return;

                scanner = new Html5Qrcode("scanner");
                const config = { fps: 10, qrbox: 250 };

                scanner.start({ facingMode: "environment" }, config, (decodedText) => {
                    // Stop scanning after successful scan
                    scanner.stop();
                    @this.dispatch('handleScan', { code: decodedText });
                });
            }

            function initCamera() {
                const video = document.getElementById('camera');
                const canvas = document.getElementById('canvas');
                const captureBtn = document.getElementById('capture-btn');
                if (!video || !canvas || !captureBtn) return;

                // Access the camera
                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                    .then(stream => {
                        cameraStream = stream;
                        video.srcObject = stream;
                    })
                    .catch(err => {
                        console.error('Error accessing camera:', err);
                        alert('Could not access camera. Please check permissions.');
                    });

                // Set up capture button
                captureBtn.addEventListener('click', () => {
                    const context = canvas.getContext('2d');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    
                    const imageData = canvas.toDataURL('image/png');
                    @this.dispatch('imageCaptured', { imageData: imageData });
                    
                    // Stop the camera stream
                    if (cameraStream) {
                        cameraStream.getTracks().forEach(track => track.stop());
                        cameraStream = null;
                    }
                });
            }

            function stopAllMediaStreams() {
                if (scanner) {
                    scanner.stop().catch(() => {});
                }
                
                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                    cameraStream = null;
                }
            }

            // Initialize scanner when page loads
            if (document.getElementById('scanner')) {
                initScanner();
            }

            // Initialize camera when in capture mode
            if (document.getElementById('camera')) {
                initCamera();
            }

            // Listen for Livewire events
            Livewire.on('restartScanning', () => {
                stopAllMediaStreams();
                setTimeout(() => {
                    if (document.getElementById('scanner')) {
                        initScanner();
                    }
                }, 500);
            });

            Livewire.on('startCaptureMode', () => {
                stopAllMediaStreams();
                setTimeout(() => {
                    if (document.getElementById('camera')) {
                        initCamera();
                    }
                }, 500);
            });

            // Clean up when component is destroyed
            document.addEventListener('livewire:navigating', () => {
                stopAllMediaStreams();
            });
        });
    </script>
    @endpush
</x-support-layout>