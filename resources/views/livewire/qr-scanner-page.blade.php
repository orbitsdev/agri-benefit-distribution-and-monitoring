<x-support-layout>
    <style>
        #qr-reader {
            position: relative !important;
            z-index: 1 !important; /* Ensures it's above any overlays */
        }

        .qr-shaded-region {
            display: none !important; /* Hide unnecessary shaded overlay */
        }
    </style>
       <div class="flex flex-col items-center min-h-screen px-4">
        <div class="w-full max-w-lg rounded-xl p-6 md:p-8">
            <h2 class="text-xl mt-8 font-semibold text-gray-900 flex items-center justify-center gap-2">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 8h4V4H3v4zm14-4v4h4V4h-4zM3 20h4v-4H3v4zm14 0h4v-4h-4v4z"></path>
                </svg>
                Scan QR Code
            </h2>

            <!-- Scanner (Hidden when Transaction Exists) -->
            @if(!$transaction)
            <div id="qr-reader"
                class="mt-4 w-full aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-300 shadow-sm
                {{ $beneficiary ? 'hidden' : '' }}">
            </div>
            @endif

            <!-- Scanned Code Display -->
            @if(!$transaction)
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">Scanned Code:</p>
                <p class="text-lg font-medium text-gray-900">{{ $scannedCode }}</p>
            </div>
            @endif

            <!-- Beneficiary Details -->
            @if($beneficiary)
            <div class="mt-4 bg-gray-50 rounded-md border border-gray-200 p-4 shadow-sm">
                <dl class="divide-y divide-gray-100">
                    <div class="py-2 flex justify-between">
                        <dt class="text-sm font-medium text-gray-600">Name</dt>
                        <dd class="text-sm text-gray-900">{{ $beneficiary->name }}</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="text-sm font-medium text-gray-600">Item</dt>
                        <dd class="text-sm text-gray-900">{{ $beneficiary->distributionItem->item->name ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>
            @endif

            <!-- ✅ Show Transaction Details After Claim -->
            @if($transaction)
            <div class="mt-6 p-4 bg-white border border-gray-200 rounded-md shadow">
                <p class="text-lg font-semibold text-gray-900">Transaction Details</p>
                <p class="text-sm text-gray-500">Transaction ID: {{ $transaction->id }}</p>
                <p class="text-sm text-gray-500">Item: {{ $transaction->distributionItem->name ?? 'N/A' }}</p>
            </div>
            @endif

            <!-- ✅ Show Image Capture After Confirmation -->
            @if($showCapture)
            <div class="mt-6 p-4 bg-white border border-gray-200 rounded-md shadow">
                <p class="text-sm text-gray-500">Take a picture as proof of claim:</p>

                <!-- Video Feed for Capturing -->
                <div id="qr-reader-container" class="relative w-full">
                    <div id="qr-reader" class="w-full aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-300 shadow-sm"></div>
                </div>

                <!-- Hidden Canvas for Capturing Image -->
                <canvas id="captureCanvas" class="hidden"></canvas>

                <!-- Capture and Skip Buttons -->
                <div class="flex items-center gap-3 mt-4">
                    <button onclick="captureImage()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                        Take Picture
                    </button>
                    <button wire:click="skip" class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                        Skip
                    </button>
                </div>
            </div>
        @endif


            <!-- ✅ Keep Reset Button -->
            <div class="mt-5 flex items-center justify-between">
                {{ $this->confirmQrAction() }}
                <button wire:click="resetScan" class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                    Reset
                </button>
            </div>

        </div>
    </div>
    <x-filament-actions::modals />
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const scannerElement = document.getElementById("qr-reader");
            const html5QrCode = new Html5Qrcode("qr-reader");
            let currentCameraId = null;
            let isScanning = false;

            // ✅ Function to start QR scanner
            async function startScanner() {
                if (isScanning) return;
                isScanning = true;

                console.log("Starting QR Scanner...");

                try {
                    await html5QrCode.start(
                        currentCameraId,
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText) => {
                            if (!isScanning) return;

                            console.log("Scanned QR Code:", decodedText);
                            isScanning = false;
                            html5QrCode.stop().then(() => console.log("Scanner stopped")).catch(console.error);

                            // ✅ Send Scanned Code to Livewire
                            Livewire.dispatch('handleScan', { code: decodedText });
                        },
                        (errorMessage) => {
                            console.warn("QR Scan Error:", errorMessage);
                        }
                    );
                } catch (err) {
                    console.error("QR scanner error:", err);
                }
            }

            // ✅ Get Camera List and Start Scanner
            Html5Qrcode.getCameras()
                .then(devices => {
                    if (devices.length === 0) {
                        console.error("No camera found.");
                        return;
                    }

                    currentCameraId = devices.find(device => device.label.toLowerCase().includes("back"))?.id || devices[0].id;
                    startScanner();
                })
                .catch(err => {
                    console.error("Camera detection error:", err);
                });

            // ✅ Restart scanner properly after confirmation/reset
            Livewire.on('restartScanning', async function () {
                console.log("Restarting scanner...");
                isScanning = false;

                try {
                    await html5QrCode.stop();
                    console.log("Scanner fully stopped, restarting...");
                    setTimeout(() => startScanner(), 500);
                } catch (err) {
                    console.error("Error stopping scanner before restart:", err);
                    startScanner();
                }
            });

            // ✅ Capture Image from Camera Stream
            function captureImage() {
                const video = document.querySelector("#qr-reader video");
                const canvas = document.getElementById("captureCanvas");
                if (!video || !canvas) {
                    console.error("Video or Canvas element not found.");
                    return;
                }

                const context = canvas.getContext("2d");
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                const imageData = canvas.toDataURL("image/png");
                Livewire.dispatch("imageCaptured", { image: imageData });

                console.log("Image Captured!");
            }

            // ✅ Assign Capture Button to Function
            document.getElementById("captureBtn")?.addEventListener("click", captureImage);
        });
    </script>









</x-support-layout>
