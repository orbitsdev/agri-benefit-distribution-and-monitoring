<div class="py-4">
    
    @if($getRecord()->code)
        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($getRecord()->code, 'QRCODE') }}" 
             alt="QR Code" 
             class="h-16 w-16 ">
    @endif
</div>
