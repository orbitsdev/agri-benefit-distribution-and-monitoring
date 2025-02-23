<?php

namespace App\Livewire;

use App\Models\Support;
use Livewire\Component;
use App\Models\Beneficiary;
use Livewire\Attributes\On;
use Filament\Actions\Action;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;

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

        // Fetch the beneficiary details
        $this->beneficiary = Beneficiary::where('code', $code)
            ->with('distributionItem.item')
            ->first();

        if (!$this->beneficiary) {
            $this->dialog()->error(
                title: 'Invalid QR Code',
                description: 'No beneficiary found for this code.'
            );
            $this->resetScan();
            return;
        }

        // Retrieve the support record using the unique code stored on the user
        $support = Support::where('unique_code', Auth::user()->code)->first();

        if (
            !$this->beneficiary->distributionItem ||
            $this->beneficiary->distributionItem->distribution_id !== $support->distribution_id
        ) {
            $this->dialog()->error(
                title: 'Invalid QR Code',
                description: 'This beneficiary does not belong to your distribution.'
            );
            $this->resetScan();
            return;
        }

        $itemName = optional($this->beneficiary->distributionItem?->item)->name ?? 'N/A';

        $this->dialog()->success(
            title: 'Scan Successful',
            description: "Beneficiary found: {$this->beneficiary->name}, Item: {$itemName}."
        );
    }


    public function confirmClaim()
    {
        if ($this->beneficiary) {
            $this->beneficiary->update(['status' => 'Claimed']);

            // ✅ Success message
            $this->dialog()->success(
                title: 'Claim Confirmed',
                description: "{$this->beneficiary->name} has successfully claimed the item."
            );

            // ✅ Reset scan & restart scanner properly
            $this->resetScan();
        }
    }

    public function resetScan()
    {
        $this->scannedCode = '';
        $this->codeDetected = false;
        $this->isScanning = true;
        $this->beneficiary = null;

        // ✅ Ensure UI updates properly & scanner restarts
        $this->dispatch('restartScanning');
    }



    public function confirmQrAction(): Action
    {
        return Action::make('confirmQr')
            ->label('Confirm Claim')
            ->icon('heroicon-o-check-circle')
            ->requiresConfirmation()
            ->color('success')
            ->visible(fn () => $this->beneficiary && $this->beneficiary->status === 'Unclaimed')
            ->action(fn () => $this->confirmClaim());
    }

    public function render()
    {
        return view('livewire.qr-scanner-page');
    }
}
