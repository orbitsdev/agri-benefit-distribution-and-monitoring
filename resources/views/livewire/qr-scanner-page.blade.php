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
            <h2 class="text-2xl font-semibold text-gray-900 flex items-center justify-center gap-2 mt-8">
                <svg class="w-7 h-7 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 8h4V4H3v4zm14-4v4h4V4h-4zM3 20h4v-4H3v4zm14 0h4v-4h-4v4z"></path>
                </svg>
                <span>Scan QR Code</span>
            </h2>

            <p class="text-lg font-medium text-gray-700 mt-2">
                {{ Auth::user()->support()?->distribution?->title ?? 'No Active Distribution' }}
            </p>


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
                    <!-- Name -->
                    <div class="py-2 flex justify-between">
                        <dt class="text-sm font-medium text-gray-600">Name</dt>
                        <dd class="text-sm text-gray-900">{{ $beneficiary->name }}</dd>
                    </div>

                    <!-- Item -->
                    <div class="py-2 flex justify-between">
                        <dt class="text-sm font-medium text-gray-600">Item</dt>
                        <dd class="text-sm text-gray-900">{{ $beneficiary->distributionItem->item->name ?? 'N/A' }}</dd>
                    </div>

                    <!-- ✅ Claim Status with Badge -->
                    <div class="py-2 flex justify-between">
                        <dt class="text-sm font-medium text-gray-600">Status</dt>
                        <dd>
                            @if($beneficiary->status === 'Claimed')
                                <span class="px-2 py-1 text-xs font-semibold text-white bg-green-600 rounded-lg">
                                    Claimed
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-white bg-red-600 rounded-lg">
                                    Unclaimed
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            @endif

            <!-- ✅ Show Transaction Details After Claim -->
            @if($showCapture)
            <div class="mt-6 p-4 bg-white border border-gray-200 rounded-md shadow">
                <p class="text-sm text-gray-500">Take a picture as proof of claim:</p>

                <!-- Video Feed for Capturing -->
                <div id="qr-reader-container" class="relative w-full">
                    <div id="qr-reader" class="w-full aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-300 shadow-sm"></div>
                </div>

                <!-- Hidden Canvas for Capturing Image -->
                <canvas id="captureCanvas" class="hidden"></canvas>

                <!-- ✅ Captured Image Preview -->
                <img id="capturedImagePreview" class="hidden mt-3 w-full rounded-md border border-gray-300" alt="Captured Image">

                <!-- Hidden Input to Store Image Data -->
                <input type="hidden" id="capturedImageData">

                <!-- ✅ Capture and Submit Buttons -->
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



<script>
    document.addEventListener('DOMContentLoaded', function () {
    const scannerElement = document.getElementById("qr-reader");
    let html5QrCode = new Html5Qrcode("qr-reader");
    let currentCameraId = null;
    let isScanning = false;

    async function startScanner() {
        if (isScanning) return;
        isScanning = true;

        console.log("📷 Starting QR Scanner...");

        try {
            await html5QrCode.start(
                currentCameraId,
                { fps: 10, qrbox: { width: 250, height: 250 } },
                (decodedText) => {
                    if (!isScanning) return;

                    console.log("✅ Scanned QR Code:", decodedText);
                    isScanning = false;
                    html5QrCode.stop().then(() => console.log("📴 Scanner stopped")).catch(console.error);

                    Livewire.dispatch('handleScan', { code: decodedText });
                },
                (errorMessage) => {
                    console.warn("⚠️ QR Scan Error:", errorMessage);
                }
            );
        } catch (err) {
            console.error("❌ QR scanner error:", err);
        }
    }

    // ✅ Get available cameras and start scanner
    Html5Qrcode.getCameras()
        .then(devices => {
            if (devices.length === 0) {
                console.error("❌ No camera found.");
                return;
            }

            currentCameraId = devices.find(device => device.label.toLowerCase().includes("back"))?.id || devices[0].id;
            startScanner();
        })
        .catch(err => {
            console.error("❌ Camera detection error:", err);
        });

    // ✅ Restart scanner properly after confirmation/reset
    Livewire.on('restartScanning', async function () {
        console.log("🔄 Restarting scanner...");
        isScanning = false;

        try {
            await html5QrCode.stop();
            console.log("📴 Scanner fully stopped, restarting...");
            setTimeout(() => startScanner(), 500);
        } catch (err) {
            console.error("❌ Error stopping scanner before restart:", err);
            startScanner();
        }
    });

    // ✅ Switch Scanner to Image Capture Mode (AFTER CLAIM CONFIRMATION)
    Livewire.on('startCaptureMode', function () {
        console.log("📸 Switching to Image Capture Mode...");
        isScanning = false;

        // ✅ Restart scanner to ensure camera is ready for picture capture
        setTimeout(() => startScanner(), 500);
    });

    // ✅ Image Capture Function - Ensures Video Element Exists
    window.captureImage = function () {
        const scannerContainer = document.getElementById("qr-reader");
        const video = scannerContainer?.querySelector("video");

        if (!video) {
            console.error("❌ No video element found. Ensure the scanner is running.");
            alert("No camera detected! Please make sure the scanner is open.");
            return;
        }

        const canvas = document.getElementById("captureCanvas");
        if (!canvas) {
            console.error("❌ Capture canvas not found.");
            return;
        }

        const context = canvas.getContext("2d");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = canvas.toDataURL("image/png");
        document.getElementById("capturedImagePreview").src = imageData;
        document.getElementById("capturedImagePreview").classList.remove("hidden");
        document.getElementById("capturedImageData").value = imageData;

        // ✅ Show the Upload button after capturing
        document.getElementById("uploadBtn").classList.remove("hidden");

        console.log("✅ Image Captured! Data:", imageData);
    };

    // ✅ Submit Captured Image
    window.submitCapturedImage = function () {
    const imageData = document.getElementById("capturedImageData").value;
    if (!imageData) {
        alert("No image captured! Please take a picture first.");
        return;
    }

    console.log("🚀 Sending Captured Image to Livewire:", imageData); // Debugging

    // ✅ Use Livewire.dispatch() for Livewire 3
    Livewire.dispatch("imageCaptured", { imageData: imageData });
};

});

</script>










</x-support-layout>
