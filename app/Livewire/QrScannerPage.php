<?php

namespace App\Livewire;

use App\Models\Beneficiary;
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
    public bool $codeDetected = false;
    public bool $isScanning = true;
    public ?Beneficiary $beneficiary = null;
    #[On('handleScan')]
    public function handleScan(string $code)
    {
        $this->scannedCode = $code;
        $this->codeDetected = true;
        $this->isScanning = false;

        // ✅ Ensure a valid beneficiary is found
        $this->beneficiary = Beneficiary::where('code', $code)->with('distributionItem.item')->first();

        if (!$this->beneficiary) {
            // ✅ Show error if beneficiary is not found
            $this->dialog()->error(
                title: 'Invalid QR Code',
                description: 'No beneficiary found for this code.'
            );
            $this->resetScan();
            return;
        }

        // ✅ Ensure relations are available before using
        $itemName = optional($this->beneficiary->distributionItem?->item)->name ?? 'N/A';

        $this->dialog()->success(
            title: 'Scan Successful',
            description: "Beneficiary found: {$this->beneficiary->name}, Item: {$itemName}."
        );
    }


    public function resetScan()
{
    $this->scannedCode = '';
    $this->codeDetected = false;
    $this->isScanning = true;
    $this->beneficiary = null;

    // ✅ Restart scanning
    $this->dispatch('restartScanning');
}


    public function confirmClaim()
    {
        if ($this->beneficiary) {
            $this->beneficiary->update(['status' => 'Claimed']);

            $this->dialog()->success(
                title: 'Claim Confirmed',
                description: "{$this->beneficiary->name} has successfully claimed the item."
            );


            $this->resetScan();
            $this->dispatch('refresh');
        }
    }


    public function confirmQrAction(): Action
    {
        return Action::make('confirmQr')
            ->label('Confirm Claim')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn () => $this->beneficiary && $this->beneficiary->status === 'Unclaimed')
            ->action(fn () => $this->confirmClaim());
    }

    public function render()
    {
        return view('livewire.qr-scanner-page');
    }
}
