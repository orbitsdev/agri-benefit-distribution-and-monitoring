<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md">
        <h2 class="text-lg font-semibold text-center text-gray-700">Scan QR Code</h2>
        <div id="scanner" class="mt-6 w-full bg-gray-200 rounded-lg overflow-hidden">
            <!-- Scanner will load here -->
        </div>
        <div class="mt-4 text-center">
            <p>Scanned Code:</p>
            <p class="text-lg font-bold text-gray-800">{{ $scannedCode }}</p>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const scanner = new Html5QrcodeScanner(
            "scanner",
            { fps: 10, qrbox: 250 }
        );

        scanner.render(
            (decodedText) => {
                // Emit the scanned code to Livewire
                Livewire.emit('handleScan', decodedText);
            },
            (errorMessage) => {
                console.error(errorMessage); // Log errors
            }
        );
    });
</script>
