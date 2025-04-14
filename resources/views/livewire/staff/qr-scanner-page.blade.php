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

            <!-- Scanner Container -->
            @if(!$beneficiary && !$transaction)
            <div id="qr-reader" class="mt-4 w-full aspect-square bg-gray-100 rounded-lg overflow-hidden border border-gray-300 shadow-sm">
            </div>
            @endif

            <!-- Scanned Code Display -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">Scanned Code:</p>
                <p class="text-lg font-medium text-gray-900">{{ $scannedCode }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="mt-5 flex items-center justify-center gap-4">
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
        let isScanning = false;

        async function startScanner() {
            if (isScanning) return;
            isScanning = true;

            console.log("Starting QR Scanner...");

            try {
                // Get available cameras
                const devices = await Html5Qrcode.getCameras();
                if (devices && devices.length === 0) {
                    console.error("No cameras found!");
                    return;
                }
                
                // Try to use back camera first, then front camera, then first available
                const cameraId = devices.find(device => device.label.toLowerCase().includes("back"))?.id || 
                                 devices.find(device => device.label.toLowerCase().includes("rear"))?.id || 
                                 devices[0].id;
                
                console.log("Using camera:", cameraId);
                
                // Start scanning
                await html5QrCode.start(
                    cameraId,
                    { 
                        fps: 10, 
                        qrbox: 250
                    },
                    (decodedText) => {
                        console.log("Scanned QR Code:", decodedText);
                        html5QrCode.stop();
                        isScanning = false;
                        
                        // Send the code to the Livewire component
                        Livewire.dispatch('handleScan', { code: decodedText });
                    },
                    (errorMessage) => {}
                );
            } catch (err) {
                console.error("QR scanner error:", err);
                isScanning = false;
            }
        }
        
        // Initialize scanner if element exists
        if (scannerElement) {
            startScanner();
        }
        
        // Listen for Livewire events
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('resetScanner', () => {
                if (html5QrCode) {
                    html5QrCode.stop().then(() => {
                        console.log("Scanner stopped and reset");
                        isScanning = false;
                        setTimeout(() => {
                            startScanner();
                        }, 500);
                    }).catch(err => {
                        console.error("Error stopping scanner:", err);
                        isScanning = false;
                    });
                }
            });
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
                alert("No camera detected! Please make sure the scanner is open and camera permissions are granted.");
                return;
            }

            const canvas = document.getElementById("captureCanvas");
            if (!canvas) {
                console.error("❌ Capture canvas not found.");
                return;
            }

            try {
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
            } catch (err) {
                console.error("❌ Error capturing image:", err);
                alert("Error capturing image: " + err.message);
            }
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

        // Start the scanner when the page loads
        startScanner();
    });
    </script>
    @endpush
</x-support-layout>