<?php

namespace App\Jobs;

use App\Mail\QrMail;
use App\Models\Beneficiary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendQrMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $beneficiary;

    /**
     * Create a new job instance.
     */
    public function __construct(Beneficiary $beneficiary)
    {
        $this->beneficiary = $beneficiary;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->beneficiary->email) {
            Log::warning('Skipped sending email: Beneficiary ' . $this->beneficiary->id . ' does not have an email.');
            return; // Exit the job if no email is available
        }

        try {
            Mail::to($this->beneficiary->email)
                ->send(new QrMail($this->beneficiary));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error sending email to ' . $this->beneficiary->email . ': ' . $e->getMessage());

            // Optionally fail the jo
            $this->fail($e);
        }
    }
}
