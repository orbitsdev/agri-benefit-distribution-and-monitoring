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
    public bool $showCapture = false; // ✅ Control capture screen

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

            // ✅ Create Transaction Record
            $this->transaction = Transaction::create([
                'beneficiary_id' => $this->beneficiary->id,
                'distribution_item_id' => $this->beneficiary->distributionItem->id ?? null,
                'status' => 'Completed',
            ]);

            $this->showCapture = true; // ✅ Show capture screen

            $this->dialog()->success(
                title: 'Claim Confirmed',
                description: "{$this->beneficiary->name} has successfully claimed the item."
            );
        }
    }

    public function uploadImage()
    {
        if ($this->image && $this->transaction) {
            $path = $this->image->store('transaction_images', 'public');

            // ✅ Save the image in Spatie Media Library
            $this->transaction->addMedia(storage_path("app/public/{$path}"))
                ->toMediaCollection('image');

            $this->dialog()->success(
                title: 'Image Uploaded',
                description: 'Proof of claim has been saved.'
            );

            $this->resetScan();
        }
    }

    public function skip()
    {
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
        $this->showCapture = false; // ✅ Hide capture screen
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
