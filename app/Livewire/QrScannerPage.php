<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Attributes\On;

class QrScannerPage extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use WireUiActions;

    public string $scannedCode = '';
    public bool $codeDetected = false; // ✅ Flag to show the confirm button
    public bool $isScanning = true; // ✅ Indicator for scanning status

    #[On('handleScan')] // ✅ Livewire 3 event listener
    public function handleScan(string $code)
    {
        $this->scannedCode = $code;
        $this->codeDetected = true; // ✅ Show the confirm button
        $this->isScanning = false; // ✅ Stop scanning indicator

        // ✅ Automatically show Filament confirmation dialog
        $this->dialog()->success(
            title: 'Scan Successful',
            description: 'Scan Successful',
        );
        dd($this->scannedCode);
    }

    public function confirmScan()
    {
        // ✅ Show WireUI success message
        $this->dialog()->success(
            title: 'QR Code Confirmed',
            description: 'The scanned QR code has been successfully processed.'
        );

        // ✅ Reset scanning state
        $this->scannedCode = '';
        $this->codeDetected = false;
        $this->isScanning = true; // ✅ Restart scanning
    }

    // ✅ Filament Action for Confirming QR Code
    public function confirmQrAction(): Action
    {
        return Action::make('confirmQr')
            ->label('Confirm QR Code')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn () => $this->codeDetected) // ✅ Only show if a code is detected
            ->action(fn () => $this->confirmScan()); // ✅ Calls confirmScan() when clicked
    }

    public function render()
    {
        return view('livewire.qr-scanner-page');
    }
}
