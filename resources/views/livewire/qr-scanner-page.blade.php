<x-support-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 px-4">
        <h2 class="text-lg font-semibold text-center text-gray-700">Scan QR Code</h2>

        <!-- Scanning Indicator -->
        <div wire:loading wire:target="handleScan" class="mt-2 text-blue-600 font-medium">
            🔍 Scanning...
        </div>

        <!-- Camera Selection -->
        <div id="camera-switch" class="hidden flex justify-center space-x-4 mt-4">
            <button id="toggle-camera" class="px-4 py-2 bg-blue-500 text-white rounded">Switch Camera</button>
        </div>

        <!-- Scanner Box -->
        <div id="qr-reader" class="mt-6 w-full max-w-md bg-gray-200 rounded-lg overflow-hidden"></div>

        <!-- Error Messages -->
        <div id="camera-error" class="hidden mt-4 text-center text-red-600 font-semibold">
            No camera detected. Please ensure your device has a camera and grant permissions.
        </div>
        <div id="permission-error" class="hidden mt-4 text-center text-red-600 font-semibold">
            Camera access denied. Please allow camera permissions in your browser settings.
        </div>
        <div id="generic-error" class="hidden mt-4 text-center text-red-600 font-semibold">
            An unexpected error occurred while accessing the camera.
        </div>

        <!-- ✅ Live Display of Scanned Code -->
        <div class="mt-4 text-center">
            <p class="text-gray-600">Scanned Code:</p>
            <p class="text-lg font-bold text-gray-800">{{ $scannedCode }}</p>
        </div>

        <!-- ✅ Filament Action Button -->
        <div class="mt-4">
            {{ $this->confirmQrAction() }}
        </div>
    </div>

    <!-- ✅ Filament Action Modals -->
    <x-filament-actions::modals />

    <!-- QR Scanner Script -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const scannerElement = document.getElementById("qr-reader");

            if (!scannerElement) {
                console.error("QR Reader element not found!");
                return;
            }

            // Initialize scanner
            const html5QrCode = new Html5Qrcode("qr-reader");
            let currentCameraId = null;

            Html5Qrcode.getCameras()
                .then(devices => {
                    if (devices.length === 0) {
                        document.getElementById("camera-error").classList.remove("hidden");
                        return;
                    }

                    currentCameraId = devices.find(device => device.label.toLowerCase().includes("back"))?.id || devices[0].id;

                    function startScanner(cameraId) {
                        html5QrCode.start(
                            cameraId,
                            { fps: 10, qrbox: { width: 250, height: 250 } },
                            (decodedText) => {
                                console.log(`Scan result: ${decodedText}`);
                                Livewire.dispatch('handleScan', decodedText); // ✅ Livewire 3 dispatch event
                            },
                            (errorMessage) => {
                                console.error("QR Scan Error:", errorMessage);
                            }
                        ).catch(err => {
                            document.getElementById("generic-error").classList.remove("hidden");
                            console.error("QR scanner error:", err);
                        });
                    }

                    // Start scanner with default camera
                    startScanner(currentCameraId);

                })
                .catch(err => {
                    document.getElementById("camera-error").classList.remove("hidden");
                    console.error("Camera detection error:", err);
                });
        });
    </script>

</x-support-layout>
