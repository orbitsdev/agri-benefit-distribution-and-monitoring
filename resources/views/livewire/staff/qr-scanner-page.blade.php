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

            <!-- Scanner -->
            @if(!$showCapture && !$transaction)
                <div id="qr-scanner"
                     class="relative w-full h-64 sm:h-72 bg-black rounded-xl overflow-hidden shadow border border-gray-300"
                     wire:ignore>
                    <!-- Placeholder while loading -->
                    <div id="scanner-placeholder"
                         class="absolute inset-0 z-10 flex items-center justify-center text-gray-300 text-sm bg-black">
                        Initializing camera...
                    </div>

                    <!-- Optional focus frame -->
                    <div class="absolute inset-0 pointer-events-none z-20 flex items-center justify-center">
                        <div class="w-1/2 h-1/2 border-4 border-white rounded-sm"></div>
                    </div>
                </div>
            @endif

            <!-- Scanned Code Display -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">Scanned Code:</p>
                <p class="text-lg font-semibold text-gray-900 break-all">{{ $scannedCode }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4 flex justify-center gap-3 flex-wrap">
                {{ $this->confirmQrAction() }}
                <button wire:click="resetScan"
                        class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                    Reset
                </button>
            </div>

            <!-- Capture Mode -->
            @if($showCapture)
                <div class="mt-6 p-4 bg-white border border-gray-200 rounded-md shadow-sm">
                    <p class="text-sm text-gray-500">Take a picture as proof of claim:</p>

                    <div id="qr-capture"
                         class="w-full aspect-video bg-black rounded-lg overflow-hidden border border-gray-300 shadow-sm mt-2"
                         wire:ignore>
                    </div>

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
            const scannerElement = document.getElementById("qr-scanner");
            const captureElement = document.getElementById("qr-capture");
            const html5QrCode = new Html5Qrcode("qr-scanner");
            let isScanning = false;

            async function startScanner() {
                if (isScanning) return;
                isScanning = true;

                try {
                    const devices = await Html5Qrcode.getCameras();
                    if (!devices.length) {
                        console.error("No cameras found!");
                        return;
                    }

                    const cameraId = devices.find(d => d.label.toLowerCase().includes("back"))?.id || devices[0].id;

                    await html5QrCode.start(
                        cameraId,
                        { fps: 10, qrbox: 250 },
                        (decodedText) => {
                            console.log("✅ QR Code:", decodedText);
                            html5QrCode.stop();
                            isScanning = false;
                            Livewire.dispatch('handleScan', { code: decodedText });

                            // Remove the placeholder once camera is active
                            document.getElementById('scanner-placeholder')?.remove();
                        },
                        (errorMessage) => {}
                    );
                } catch (err) {
                    console.error("Scanner error:", err);
                }
            }

            startScanner();

            Livewire.on('restartScanning', async () => {
                console.log("🔁 Restarting scanner...");
                try {
                    await html5QrCode.stop();
                } catch (e) {}
                isScanning = false;
                setTimeout(() => startScanner(), 500);
            });

            Livewire.on('startCaptureMode', () => {
                console.log("📸 Switching to capture mode...");
                isScanning = false;
                setTimeout(() => startScanner(), 500);
            });

            window.captureImage = function () {
                const video = captureElement?.querySelector("video");
                if (!video) {
                    alert("Camera not ready for capture.");
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
                if (!imageData) {
                    alert("No image data found!");
                    return;
                }
                Livewire.dispatch("imageCaptured", { imageData });
            };
        });
    </script>
</x-support-layout>
