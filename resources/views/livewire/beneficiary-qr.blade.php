<div class="flex items-center justify-center min-h-screen bg-gray-200">

    
        @if($record->code)
            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($record->code, 'QRCODE') }}" 
                 alt="QR Code" 
                 class="w-72 h-72">
        @else
          
        @endif
    
</div>
