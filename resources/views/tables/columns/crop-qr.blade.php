<div class="py-4">
    @if (isset($getRecord()->cropsToReceive->unique_code))
        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($getRecord()->cropsToReceive->unique_code, 'QRCODE') }}"
             alt="QR Code"
             class="h-16 w-16 ">
    @endif
</div>

