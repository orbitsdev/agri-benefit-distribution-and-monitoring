<?php

namespace App\Livewire;

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
    public bool $showCapture = false; // ✅ Controls capture screen
    public ?Distribution $distribution = null;

    #[On('handleScan')]
    public function handleScan(string $code)
    {
        if ($this->showCapture) return; // ✅ Prevent scanning when capturing image

        $this->scannedCode = $code;
        $this->codeDetected = true;
        $this->isScanning = false;

        // ✅ Check if the user has an active support record
        $support = Auth::user()->support();

        if (!$support || !$support->distribution) {
            $this->dialog()->warning(
                title: 'No Active Distribution',
                description: 'You are not assigned to an active distribution. Please contact the administrator.'
            );
            $this->resetScan();
            return;
        }

        $distribution = $support->distribution;

        // ✅ Ensure distribution has a valid status
        if (!in_array($distribution->status, [Distribution::STATUS_ONGOING, Distribution::STATUS_COMPLETED])) {
            $this->dialog()->warning(
                title: 'Distribution Not Available',
                description: 'You can only scan beneficiaries when the distribution is Ongoing or Completed.'
            );
            $this->resetScan();
            return;
        }

        // ✅ Find Beneficiary with Valid Distribution
        $this->beneficiary = Beneficiary::where('code', $code)
            ->whereHas('distributionItem.distribution', function ($query) use ($distribution) {
                $query->where('id', $distribution->id)
                    ->whereIn('status', [Distribution::STATUS_ONGOING, Distribution::STATUS_COMPLETED]);
            })
            ->with('distributionItem.item')
            ->first();

        // ✅ Handle Invalid QR Code
        if (!$this->beneficiary) {
            $this->dialog()->error(
                title: 'Invalid QR Code',
                description: 'No valid beneficiary found for this QR code. Please check and try again.'
            );
            $this->resetScan();
            return;
        }

        // ✅ Success Message
        $this->dialog()->success(
            title: 'Scan Successful',
            description: "Beneficiary found: {$this->beneficiary->name}"
        );
    }


    public function confirmClaim()
    {
        if ($this->beneficiary) {
            DB::beginTransaction(); // ✅ Start Transaction

            try {
                // ✅ Update Beneficiary Status to "Claimed"
                $this->beneficiary->update(['status' => 'Claimed']);

                // ✅ Retrieve Related Details
                $distributionItemDetails = $this->beneficiary->distributionItem ?? null;
                $distributionDetails = $this->beneficiary->distributionItem?->distribution ?? null;
                $barangayDetails = $distributionDetails?->barangay ?? null;
                $supportDetails = Auth::user()->support() ?? null;
                $currentUser = Auth::user();

                // ✅ Store Only Essential Data in JSON
                $recorderDetails = [
                    'id' => $currentUser->id,
                    'name' => $currentUser->name,
                    'role' => $currentUser->role,
                    'email' => $currentUser->email,
                    'unique_code' => $currentUser->role === User::MEMBER ? $supportDetails->unique_code : null,
                    'enable_item_scanning' => $currentUser->role === User::MEMBER ? ($supportDetails->enable_item_scanning ? 'Yes' : 'No') : null,
                    'enable_beneficiary_management' => $currentUser->role === User::MEMBER ? ($supportDetails->enable_beneficiary_management ? 'Yes' : 'No') : null,
                ];

                // ✅ Create Transaction Entry
                $this->transaction = Transaction::create([
                    'barangay_id' => $distributionDetails->barangay_id ?? null,
                    'distribution_id' => $distributionDetails->id ?? null,
                    'beneficiary_id' => $this->beneficiary->id,
                    'distribution_item_id' => $distributionItemDetails->id ?? null,
                    'support_id' => $supportDetails->id ?? null,
                    'admin_id' => $currentUser->role === User::ADMIN ? $currentUser->id : null,
                    'action' => 'Claimed',
                    'performed_at' => now(),

                    // ✅ Store Snapshots as an array (Laravel handles JSON conversion automatically)
                    'barangay_details' => $barangayDetails ? $barangayDetails->toArray() : null,
                    'distribution_details' => $distributionDetails ? [
                        'id' => $distributionDetails->id,
                        'title' => $distributionDetails->title,
                        'location' => $distributionDetails->location,
                        'date' => $distributionDetails->distribution_date,
                        'code' => $distributionDetails->code,
                        'status' => $distributionDetails->status,
                    ] : null,
                    'distribution_item_details' => $distributionItemDetails ? [
                        'id' => $distributionItemDetails->id,
                        'name' => $distributionItemDetails->item->name, // ✅ Extract item name instead of full object
                        'quantity' => $distributionItemDetails->quantity,
                    ] : null,
                    'beneficiary_details' => [
                        'id' => $this->beneficiary->id,
                        'name' => $this->beneficiary->name,
                        'contact' => $this->beneficiary->contact,
                        'address' => $this->beneficiary->address,
                        'email' => $this->beneficiary->email,
                        'code' => $this->beneficiary->code,
                    ],
                    'support_details' => $supportDetails ? [
                        'id' => $supportDetails->id,
                        'name' => $supportDetails->personnel->user->name,
                        'type' => $supportDetails->type,
                        'unique_code' => $supportDetails->unique_code,
                    ] : null,
                    'recorder_details' => $recorderDetails, // ✅ No need to json_encode() if casted correctly
                ]);

                DB::commit(); // ✅ Commit the transaction if everything is successful

                // ✅ Success Message
                $this->dialog()->success(
                    title: 'Claim Confirmed',
                    description: "{$this->beneficiary->name} has successfully claimed the item."
                );

                // ✅ Switch to Image Capture Mode
                $this->isScanning = false;
                $this->showCapture = true;

                // ✅ Dispatch event to restart scanner for image capture
                $this->dispatch('startCaptureMode');

            } catch (\Exception $e) {
                DB::rollBack(); // ❌ Rollback in case of error
                report($e); // ✅ Log the error

                $this->dialog()->error(
                    title: 'Error',
                    description: 'An error occurred while processing the claim. Please try again later. ' . $e->getMessage()
                );
            }
        }
    }



    #[On('imageCaptured')]
    public function uploadImage(string $imageData = null) // ✅ Accepts a string instead of an array
    {
        if (!$imageData) {
            $this->dialog()->error(
                title: 'Upload Failed',
                description: 'No image data received!'
            );
            return;
        }

        // 🔍 Debugging: Check if data is received


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
            ->visible(fn () => $this->beneficiary && $this->beneficiary->status === 'Unclaimed')
            ->action(fn () => $this->confirmClaim());
    }

    public function render()
    {
        return view('livewire.qr-scanner-page');
    }
}
