<?php

namespace App\Observers;

use Illuminate\Support\Str;
use App\Models\CropsToReceived;

class CropsToReceivedObserver
{
    public function created(CropsToReceived $cropsToReceived): void
    {
        $this->generateUniqueCode($cropsToReceived);
    }

    /**
     * Generate a unique code for the crops to be received.
     */
    private function generateUniqueCode(CropsToReceived $cropsToReceived): void
    {
        if (is_null($cropsToReceived->unique_code)) {
            $cropId = str_pad($cropsToReceived->crop_id, 3, '0', STR_PAD_LEFT); // Pads to 3 digits
            $beneficiaryId = str_pad($cropsToReceived->beneficiary_id, 5, '0', STR_PAD_LEFT); // Pads to 5 digits
            $randomPart = strtoupper(Str::random(4)); // 4-character random string

            $uniqueCode = "CRP-{$cropId}-{$beneficiaryId}-{$randomPart}";

            $cropsToReceived->unique_code = $uniqueCode;
            $cropsToReceived->save();
        }
    }
}
