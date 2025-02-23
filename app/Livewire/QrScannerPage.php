<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use App\Models\Transaction;
use Filament\Actions\Action;
use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Attributes\On;

class QrScannerPage extends Component implements HasForms, HasActions
{
    use InteractsWithActions, InteractsWithForms, WireUiActions, WithFileUploads;

    public string $scannedCode = '';
    public bool $codeDetected = false;
    public bool $isScanning = true;
    public ?Beneficiary $beneficiary = null;
    public ?Transaction $transaction = null;
    public $image = null;
    public bool $showCapture = false; // ✅ Controls capture screen

    #[On('handleScan')]
    public function handleScan(string $code)
    {
        $this->scannedCode = $code;
        $this->codeDetected = true;
        $this->isScanning = false;

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

        $this->dialog()->success(
            title: 'Scan Successful',
            description: "Beneficiary found: {$this->beneficiary->name}"
        );
    }

    public function confirmClaim()
    {
        if ($this->beneficiary) {
            $this->beneficiary->update(['status' => 'Claimed']);

            // ✅ Create Transaction Entry
            $this->transaction = Transaction::create([
                'beneficiary_id' => $this->beneficiary->id,
                'distribution_item_id' => $this->beneficiary->distributionItem->id,
                'status' => 'Claimed',
            ]);

            // ✅ Show success message
            $this->dialog()->success(
                title: 'Claim Confirmed',
                description: "{$this->beneficiary->name} has successfully claimed the item."
            );

            // ✅ Show Capture Screen
            $this->showCapture = true;
        }
    }

    public function uploadImage()
    {
        if ($this->image && $this->transaction) {
            $this->transaction->addMedia($this->image->getRealPath())->toMediaCollection('image');

            $this->dialog()->success(
                title: 'Image Uploaded',
                description: 'Proof of claim has been successfully uploaded.'
            );

            $this->resetScan();
        }
    }

    public function skip()
    {
        $this->dialog()->info(
            title: 'Skipping Proof Upload',
            description: 'You can still upload proof later if needed.'
        );

        $this->resetScan();
    }

    public function resetScan()
    {
        $this->scannedCode = '';
        $this->codeDetected = false;
        $this->isScanning = true;
        $this->beneficiary = null;
        $this->transaction = null;
        $this->image = null;
        $this->showCapture = false;

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
