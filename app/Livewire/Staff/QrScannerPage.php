<?php

namespace App\Livewire\Staff;

use App\Models\User;
use Livewire\Component;
use App\Models\Beneficiary;
use App\Models\Transaction;
use Livewire\Attributes\On;
use App\Models\Distribution;
use Filament\Actions\Action;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;

class QrScannerPage extends Component implements HasForms, HasActions
{
    use InteractsWithActions, InteractsWithForms, WireUiActions, WithFileUploads;

    public string $scannedCode = '';
    public bool $codeDetected = false;
    public bool $isScanning = true;
    public ?Beneficiary $beneficiary = null;
    public ?Transaction $transaction = null;
    public ?string $imageData = null; // Stores base64 image
    public bool $showCapture = false; // Controls capture screen

    #[On('handleScan')]
    public function handleScan(string $code)
    {
        if ($this->showCapture) return; // Prevent scanning when capturing image

        $this->scannedCode = $code;
        $this->codeDetected = true;
        $this->isScanning = false;

        // Find the beneficiary by their cropsToReceive unique code
        $this->beneficiary = Beneficiary::whereHas('cropsToReceive', function ($query) use ($code) {
            $query->where('unique_code', $code);
        })
        ->with(['cropsToReceive.crop', 'barangayDistribution.distribution'])
        ->first();

        // Handle Invalid QR Code
        if (!$this->beneficiary) {
            $this->dialog()->error(
                title: 'Invalid QR Code',
                description: 'No valid beneficiary found for this QR code. Please check and try again.'
            );
            $this->resetScan();
            return;
        }
        
        // Check if the beneficiary belongs to the staff's barangay
        if ($this->beneficiary->barangayDistribution->barangay_id !== Auth::user()->barangay_id) {
            $this->dialog()->error(
                title: 'Access Denied',
                description: 'This beneficiary does not belong to your assigned barangay.'
            );
            $this->resetScan();
            return;
        }

        // Check if already claimed
        if ($this->beneficiary->cropsToReceive->is_claimed) {
            $this->dialog()->warning(
                title: 'Already Claimed',
                description: 'This benefit has already been claimed on ' . 
                    ($this->beneficiary->cropsToReceive->date_claimed ? 
                    $this->beneficiary->cropsToReceive->date_claimed->format('M d, Y h:i A') : 'an earlier date')
            );
            $this->resetScan();
            return;
        }

        // Success Message
        $this->dialog()->success(
            title: 'Scan Successful',
            description: "Beneficiary found: {$this->beneficiary->first_name} {$this->beneficiary->last_name}"
        );
    }

    public function confirmClaim()
    {
        if ($this->beneficiary) {
            DB::beginTransaction(); // Start Transaction

            try {
                // Get the crop to update inventory
                $crop = $this->beneficiary->cropsToReceive->crop;

                // Update crops to receive status
                $this->beneficiary->cropsToReceive->is_claimed = true;
                $this->beneficiary->cropsToReceive->date_claimed = now();
                $this->beneficiary->cropsToReceive->save();

                // Decrease the crop inventory using the helper method
                $result = $crop->decreaseInventory();

                if (!$result) {
                    throw new \Exception('Cannot decrease inventory. Stock limit reached or no stock available.');
                }

                // Record transaction
                $this->transaction = Transaction::recordClaim($this->beneficiary, 'claim');

                // Dispatch events to refresh other components
                $this->dispatch('beneficiary-claimed', distribution: $this->beneficiary->barangay_distribution_id);

                // Commit the transaction
                DB::commit();

                // Success Message
                $this->dialog()->success(
                    title: 'Claim Confirmed',
                    description: "{$this->beneficiary->first_name} {$this->beneficiary->last_name} has successfully claimed the item."
                );

                // Switch to Image Capture Mode
                $this->isScanning = false;
                $this->showCapture = true;

                // Dispatch event to restart scanner for image capture
                $this->dispatch('startCaptureMode');

            } catch (\Exception $e) {
                DB::rollBack(); // Rollback in case of error
                report($e); // Log the error

                $this->dialog()->error(
                    title: 'Error',
                    description: 'An error occurred while processing the claim. Please try again later. ' . $e->getMessage()
                );
            }
        }
    }

    #[On('imageCaptured')]
    public function uploadImage(string $imageData = null)
    {
        if (!$imageData) {
            $this->dialog()->error(
                title: 'Upload Failed',
                description: 'No image data received!'
            );
            return;
        }

        $this->imageData = $imageData;

        if ($this->transaction) {
            // Convert Base64 to File
            $image = str_replace('data:image/png;base64,', '', $imageData);
            $image = base64_decode($image);
            $tempFile = tempnam(sys_get_temp_dir(), 'upload_');
            file_put_contents($tempFile, $image);

            // Store Image in Media Library
            $this->transaction->addMedia($tempFile)->toMediaCollection('image');

            $this->dialog()->success(
                title: 'Image Uploaded',
                description: 'Proof of claim has been successfully uploaded.'
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
        $this->imageData = null;
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
            ->visible(fn () => $this->beneficiary && !$this->beneficiary->cropsToReceive->is_claimed)
            ->action(fn () => $this->confirmClaim());
    }

    public function render()
    {
        return view('livewire.staff.qr-scanner-page');
    }
}
