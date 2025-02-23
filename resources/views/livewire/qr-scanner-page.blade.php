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

            <!-- Scanner (Hidden when Beneficiary is Found) -->
            <div id="qr-reader"
                class="mt-4 w-full aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-300 shadow-sm
                {{ $beneficiary ? 'hidden' : '' }}">
            </div>

            <!-- Scanned Code Display -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">Scanned Code:</p>
                <p class="text-lg font-medium text-gray-900">{{ $scannedCode }}</p>
            </div>

            <!-- Beneficiary Details -->
            @if($beneficiary)
            <div class="mt-4 bg-gray-50 rounded-md border border-gray-200 p-4 shadow-sm">
                <dl class="divide-y divide-gray-100">
                    <div class="py-2 flex justify-between">
                        <dt class="text-sm font-medium text-gray-600">Name</dt>
                        <dd class="text-sm text-gray-900">{{ $beneficiary->name }}</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="text-sm font-medium text-gray-600">Contact</dt>
                        <dd class="text-sm text-gray-900">{{ $beneficiary->contact }}</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="text-sm font-medium text-gray-600">Item</dt>
                        <dd class="text-sm text-gray-900">{{ $beneficiary->distributionItem->item->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="text-sm font-medium text-gray-600">Status</dt>
                        <dd>
                            <span class="px-3 py-1 text-xs font-medium rounded-md text-white
                                {{ $beneficiary->status === 'Claimed' ? 'bg-green-500' : 'bg-red-500' }}">
                                {{ $beneficiary->status }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
            @endif

            <!-- Capture Image -->
            @if($showCapture)
            <div class="mt-4">
                <p class="text-sm text-gray-500">Take a picture as proof of claim:</p>
                <input type="file" wire:model="image" accept="image/*" class="mt-2">
                <div class="flex items-center gap-3 mt-4">
                    <button wire:click="uploadImage"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                        Upload Picture
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

            if (!scannerElement) {
                console.error("QR Reader element not found!");
                return;
            }

            const html5QrCode = new Html5Qrcode("qr-reader");
            let currentCameraId = null;
            let isScanning = false;

            async function startScanner() {
                if (isScanning) return;
                isScanning = true;

                console.log("Starting scanner...");

                try {
                    await html5QrCode.start(
                        currentCameraId,
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText) => {
                            if (!isScanning) return;

                            console.log("Scanned QR Code:", decodedText);
                            isScanning = false;
                            html5QrCode.stop().then(() => console.log("Scanner stopped")).catch(console.error);

                            Livewire.dispatch('handleScan', { code: decodedText });
                        },
                        (errorMessage) => {
                            // console.error("QR Scan Error:", errorMessage);
                        }
                    );
                } catch (err) {
                    console.error("QR scanner error:", err);
                }
            }

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
        });
    </script>








</x-support-layout>
