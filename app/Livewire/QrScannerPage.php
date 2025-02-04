<?php

namespace App\Livewire;

use Livewire\Component;
use WireUi\Traits\WireUiActions;

class QrScannerPage extends Component
{
    use WireUiActions;

    public string $scannedCode = '';

    public function handleScan(string $code)
    {
        $this->scannedCode = $code;

        // Display success notification using WireUI
        $this->notification()->success(
            'QR Code Scanned',
            'Scanned Code: ' . $code
        );
    }

    public function render()
    {
        return view('livewire.qr-scanner-page');
    }
}
