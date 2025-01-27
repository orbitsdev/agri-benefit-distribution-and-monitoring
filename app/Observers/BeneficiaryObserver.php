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

       
        if (!$beneficiary->distribution_item || !$beneficiary->distribution_item->distribution_id) {
            logger()->warning('Distribution item or distribution ID is missing for beneficiary ID: ' . $beneficiary->id);
            return; // Skip code generation if data is incomplete
        }

        $distributionId = str_pad($beneficiary->distribution_item->distribution_id, 3, '0', STR_PAD_LEFT); // Pads Distribution ID to 3 digits
        $itemId = str_pad($beneficiary->distribution_item_id, 2, '0', STR_PAD_LEFT); // Pads Item ID to 2 digits
        $beneficiaryId = str_pad($beneficiary->id, 3, '0', STR_PAD_LEFT); // Pads Beneficiary ID to 3 digits
        $shortUuid = strtoupper(substr(Str::uuid(), 0, 8)); // Generates an 8-character unique string

        $beneficiary->code = "BEN-{$distributionId}-{$itemId}-{$beneficiaryId}-{$shortUuid}";
        $beneficiary->saveQuietly();
    }

    /**
     * Handle the Beneficiary "updated" event.
     */
    public function updated(Beneficiary $beneficiary): void
    {
        if (is_null($beneficiary->code) && $beneficiary->distribution_item) {
            $distributionId = str_pad($beneficiary->distribution_item->distribution_id, 3, '0', STR_PAD_LEFT);
            $itemId = str_pad($beneficiary->distribution_item_id, 2, '0', STR_PAD_LEFT);
            $beneficiaryId = str_pad($beneficiary->id, 3, '0', STR_PAD_LEFT);
            $shortUuid = strtoupper(substr(Str::uuid(), 0, 8));

            $beneficiary->code = "BEN-{$distributionId}-{$itemId}-{$beneficiaryId}-{$shortUuid}";
            $beneficiary->saveQuietly();
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
