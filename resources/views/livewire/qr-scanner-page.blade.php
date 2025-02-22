<x-support-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 px-4">
        <h2 class="text-lg font-semibold text-center text-gray-700">Scan QR Code</h2>

        <!-- Scanning Indicator -->
        <div wire:loading wire:target="handleScan" class="mt-2 text-blue-600 font-medium">
            🔍 Scanning...
        </div>

        <!-- Scanner Box -->
        <div id="qr-reader" class="mt-6 w-full max-w-md bg-gray-200 rounded-lg overflow-hidden"></div>

        <!-- Scanned Code Display -->
        <div class="mt-4 text-center">
            <p class="text-gray-600">Scanned Code:</p>
            <p class="text-lg font-bold text-gray-800">{{ $scannedCode }}</p>
        </div>

        <!-- Beneficiary Details -->
        @if($beneficiary)
        <div class="mt-4 p-4 bg-white rounded shadow">
            <h3 class="text-lg font-bold text-gray-700">Beneficiary Details</h3>
            <p><strong>Name:</strong> {{ $beneficiary->name }}</p>
            <p><strong>Contact:</strong> {{ $beneficiary->contact }}</p>
            <p><strong>Email:</strong> {{ $beneficiary->email }}</p>
            <p><strong>Address:</strong> {{ $beneficiary->address }}</p>
            <p><strong>Item:</strong> {{ $beneficiary->distributionItem->item->name ?? 'N/A' }}</p>
            <p>
                <strong>Status:</strong>
                <span class="px-2 py-1 rounded text-white {{ $beneficiary->status === 'Claimed' ? 'bg-green-500' : 'bg-red-500' }}">
                    {{ $beneficiary->status }}
                </span>
            </p>
        </div>
        @endif

        <!-- Buttons -->
        <div class="mt-4 flex space-x-2">
            {{ $this->confirmQrAction() }}
            <button wire:click="resetScan" class="px-4 py-2 bg-gray-500 text-white rounded">Reset</button>
        </div>
    </div>

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
            let isScanning = false; // ✅ Fix: Ensure accurate scanning state

            async function startScanner() {
                if (isScanning) return; // ✅ Prevent multiple start calls
                isScanning = true;

                console.log("Starting scanner...");

                try {
                    await html5QrCode.start(
                        currentCameraId,
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText) => {
                            if (!isScanning) return;

                            console.log("Scanned QR Code:", decodedText);
                            isScanning = false; // ✅ Stop further scanning
                            html5QrCode.stop().then(() => console.log("Scanner stopped")).catch(console.error);

                            // ✅ Dispatch scanned code to Livewire
                            Livewire.dispatch('handleScan', { code: decodedText });
                        },
                        (errorMessage) => {
                            console.error("QR Scan Error:", errorMessage);
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
                    startScanner(); // ✅ Start scanner on page load
                })
                .catch(err => {
                    console.error("Camera detection error:", err);
                });

            // ✅ Restart scanning when Livewire tells it to
            Livewire.on('restartScanning', async function () {
                console.log("Restarting scanner...");
                isScanning = false; // ✅ Ensure the state is reset

                try {
                    await html5QrCode.stop();
                    console.log("Scanner fully stopped, restarting...");
                    setTimeout(() => startScanner(), 500); // ✅ Small delay ensures smooth restart
                } catch (err) {
                    console.error("Error stopping scanner before restart:", err);
                    startScanner(); // ✅ Ensure scanner starts even if stop fails
                }
            });
        });
    </script>






</x-support-layout>
