<x-support-layout>
    <div class="flex flex-col items-center min-h-screen px-4">
        <div class="w-full max-w-lg rounded-xl p-6 md:p-8">
            <h2 class="text-2xl font-semibold text-gray-900 flex items-center justify-center gap-2 mt-8">
                <svg class="w-7 h-7 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 8h4V4H3v4zm14-4v4h4V4h-4zM3 20h4v-4H3v4zm14 0h4v-4h-4v4z"></path>
                </svg>
                <span>QR Scanner</span>
            </h2>

            <a href="{{ route('staff.dashboard') }}" class="block text-center text-sm text-blue-600 hover:text-blue-800 mt-2">
                BACK TO DASHBOARD
            </a>

            <!-- SCANNER MODE -->
            @if(!$showCapture && !$transaction)
                <div id="qr-reader" class="mt-4 w-full aspect-square bg-gray-100 rounded-lg overflow-hidden border border-gray-300 shadow-sm"></div>
            @endif

            <!-- Scanned Code Display -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">Scanned Code:</p>
                <p class="text-lg font-medium text-gray-900">{{ $scannedCode }}</p>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="mt-5 flex items-center justify-center gap-4">
                {{ $this->confirmQrAction() }}
                <button wire:click="resetScan" class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                    Reset
                </button>
            </div>

            <!-- CAPTURE MODE -->
            @if($showCapture)
                <div class="mt-6 p-4 bg-white border border-gray-200 rounded-md shadow">
                    <p class="text-sm text-gray-500">Take a picture as proof of claim:</p>

                    <div id="qr-reader" class="w-full aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-300 shadow-sm mt-2"></div>

                    <!-- Hidden Canvas for Capturing -->
                    <canvas id="captureCanvas" class="hidden"></canvas>

                    <!-- Captured Image Preview -->
                    <img id="capturedImagePreview" class="hidden mt-3 w-full rounded-md border border-gray-300" alt="Captured Image">

                    <input type="hidden" id="capturedImageData">

                    <!-- Buttons -->
                    <div class="flex items-center gap-3 mt-4">
                        <button onclick="captureImage()" id="takePictureBtn" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            Take Picture
                        </button>
                        <button onclick="submitCapturedImage()" id="uploadBtn" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 hidden">
                            Confirm Upload
                        </button>
                        <button wire:click="skip" class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
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
                        },
                        (errorMessage) => {}
                    );
                } catch (err) {
                    console.error("Scanner error:", err);
                }
            }

            // Start immediately
            startScanner();

            Livewire.on('restartScanning', async () => {
                try {
                    await html5QrCode.stop();
                } catch {}
                isScanning = false;
                setTimeout(() => startScanner(), 500);
            });

            Livewire.on('startCaptureMode', () => {
                isScanning = false;
                setTimeout(() => startScanner(), 500);
            });

            window.captureImage = function () {
                const scanner = document.getElementById("qr-reader");
                const video = scanner?.querySelector("video");

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
                if (!imageData) {
                    alert("No image data found!");
                    return;
                }

                Livewire.dispatch("imageCaptured", { imageData });
            };
        });
    </script>
</x-support-layout>
