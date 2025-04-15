<x-support-layout>
    <div class="flex flex-col items-center min-h-screen px-4 py-6 bg-white">
        <div class="w-full max-w-sm sm:max-w-md rounded-xl p-4 sm:p-6 bg-white shadow">

            <!-- Header -->
            <div class="text-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center justify-center gap-2">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 8h4V4H3v4zm14-4v4h4V4h-4zM3 20h4v-4H3v4zm14 0h4v-4h-4v4z"></path>
                    </svg>
                    <span>QR Scanner</span>
                </h2>
                <a href="{{ route('staff.dashboard') }}"
                   class="text-sm text-blue-600 hover:text-blue-800 mt-1 inline-block">
                    BACK TO DASHBOARD
                </a>
            </div>

            <!-- SCANNER MODE -->
            @if(!$showCapture && !$transaction)
                <div id="qr-reader"
                     class="relative w-full h-64 sm:h-72 bg-black rounded-xl overflow-hidden shadow border border-gray-300"
                     wire:ignore>
                    <div id="scanner-placeholder"
                         class="absolute inset-0 z-10 flex items-center justify-center text-gray-300 text-sm bg-black">
                        Initializing camera...
                    </div>
                    <div class="absolute inset-0 pointer-events-none z-20 flex items-center justify-center">
                        <div class="w-1/2 h-1/2 border-4 border-white rounded-sm"></div>
                    </div>
                </div>
            @endif

            <!-- Scanned Code -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">Scanned Code:</p>
                <p class="text-lg font-semibold text-gray-900 break-all">{{ $scannedCode }}</p>
            </div>

            <!-- Beneficiary Details -->
            @if($beneficiary)
                <div class="mt-4 bg-gray-50 rounded-md border border-gray-200 p-4 shadow-sm">
                    <dl class="divide-y divide-gray-100">
                        <div class="py-2 flex justify-between">
                            <dt class="text-sm font-medium text-gray-600">Name</dt>
                            <dd class="text-sm text-gray-900">
                                {{ $beneficiary->first_name }} {{ $beneficiary->middle_name }} {{ $beneficiary->last_name }}
                            </dd>
                        </div>
                        <div class="py-2 flex justify-between">
                            <dt class="text-sm font-medium text-gray-600">Crop</dt>
                            <dd class="text-sm text-gray-900">
                                {{ $beneficiary->cropsToReceive->crop->name ?? 'N/A' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-4 flex justify-center gap-3 flex-wrap">
                {{ $this->confirmQrAction() }}
                <button wire:click="resetScan"
                        class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                    Reset
                </button>
            </div>

            <!-- CAPTURE MODE -->
            @if($showCapture)
                <div class="mt-6 p-4 bg-white border border-gray-200 rounded-md shadow-sm">
                    <p class="text-sm text-gray-500">Take a picture as proof of claim:</p>

                    <canvas id="captureCanvas" class="hidden"></canvas>
                    <img id="capturedImagePreview"
                         class="hidden mt-3 w-full rounded-md border border-gray-300"
                         alt="Captured Image">
                    <input type="hidden" id="capturedImageData">

                    <div class="flex flex-wrap justify-center items-center gap-2 mt-4">
                        <button onclick="captureImage()" id="takePictureBtn"
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            Take Picture
                        </button>
                        <button onclick="submitCapturedImage()" id="uploadBtn"
                                class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 hidden">
                            Confirm Upload
                        </button>
                        <button wire:click="skip"
                                class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                            Skip
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <x-filament-actions::modals />

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const scannerElement = document.getElementById("qr-reader");
            let html5QrCode = new Html5Qrcode("qr-reader");
            let currentCameraId = null;
            let isScanning = false;

            async function startScanner() {
                if (isScanning) return;
                isScanning = true;

                try {
                    await html5QrCode.start(
                        currentCameraId,
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText) => {
                            if (!isScanning) return;
                            isScanning = false;
                            html5QrCode.stop().then(() => {
                                console.log("Scanner stopped");
                            }).catch(console.error);
                            Livewire.dispatch('handleScan', { code: decodedText });
                        },
                        (errorMessage) => {
                            console.warn("QR Scan Error:", errorMessage);
                        }
                    );
                } catch (err) {
                    console.error("Scanner Error:", err);
                }
            }

            Html5Qrcode.getCameras().then(devices => {
                if (!devices.length) {
                    alert("No camera detected.");
                    return;
                }
                currentCameraId = devices.find(d => d.label.toLowerCase().includes("back"))?.id || devices[0].id;
                startScanner();
            });

            Livewire.on('restartScanning', async () => {
                try {
                    await html5QrCode.stop();
                    html5QrCode.clear();
                } catch (e) {}
                isScanning = false;
                setTimeout(() => startScanner(), 500);
            });

            Livewire.on('startCaptureMode', async () => {
                try {
                    await html5QrCode.stop();
                    html5QrCode.clear();
                } catch (e) {}
                isScanning = false;
                setTimeout(() => startScanner(), 500);
            });

            window.captureImage = function () {
                const video = scannerElement?.querySelector("video");
                if (!video) {
                    alert("Camera not ready!");
                    return;
                }
                const canvas = document.getElementById("captureCanvas");
                const context = canvas.getContext("2d");
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                const imageData = canvas.toDataURL("image/png");
                document.getElementById("capturedImagePreview").src = imageData;
                document.getElementById("capturedImagePreview").classList.remove("hidden");
                document.getElementById("capturedImageData").value = imageData;
                document.getElementById("uploadBtn").classList.remove("hidden");
            };

            window.submitCapturedImage = function () {
                const imageData = document.getElementById("capturedImageData").value;
                if (!imageData || !imageData.startsWith("data:image")) {
                    alert("No image captured! Please take a picture first.");
                    return;
                }
                Livewire.dispatch("imageCaptured", imageData);
            };
        });
    </script>
</x-support-layout>
