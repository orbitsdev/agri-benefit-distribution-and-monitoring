<?php

namespace App\Observers;

use App\Models\Beneficiary;
use Illuminate\Support\Str;

class BeneficiaryObserver
{
    /**
     * Handle the Beneficiary "created" event.
     */
    public function created(Beneficiary $beneficiary): void
    {   
        $this->generateUniqueCode($beneficiary);
    }

    /**
     * Handle the Beneficiary "updated" event.
     */
    public function updated(Beneficiary $beneficiary): void
    {
        if (is_null($beneficiary->code)) {
            $this->generateUniqueCode($beneficiary);
        }

    }


    private function generateUniqueCode(Beneficiary $beneficiary): void
    {
      
        
        if (is_null($beneficiary->code)) {
            $distributionItemId = str_pad($beneficiary->distribution_item_id, 3, '0', STR_PAD_LEFT); // Pads to 3 digits
            $beneficiaryId = str_pad($beneficiary->id, 5, '0', STR_PAD_LEFT); // Pads to 5 digits
            $randomPart = strtoupper(Str::random(5)); // Generates a random string of 5 characters

            // Combine parts to create the unique code
            $uniqueCode = "BEN-{$distributionItemId}-{$beneficiaryId}-{$randomPart}";

            // Save the unique code
            $beneficiary->code = $uniqueCode;
            $beneficiary->save();
        }
    }

    /**
     * Handle the Beneficiary "deleted" event.
     */
    public function deleted(Beneficiary $beneficiary): void
    {
        //
    }

    /**
     * Handle the Beneficiary "restored" event.
     */
    public function restored(Beneficiary $beneficiary): void
    {
        //
    }

    /**
     * Handle the Beneficiary "force deleted" event.
     */
    public function forceDeleted(Beneficiary $beneficiary): void
    {
        //
    }
}
