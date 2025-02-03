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

        // Perform actions such as marking the beneficiary as claimed or saving the transaction
        $this->notification()->success('QR Code Scanned', 'Code: ' . $code);
    }

    public function render()
    {
        return view('livewire.qr-scanner-page');
    }
}
