<?php

namespace App\Observers;

use Dotenv\Util\Str;
use App\Models\Support;

class SupportObserver
{
    /**
     * Handle the Support "created" event.
     */
    public function created(Support $support): void
    {
        $distributionId = str_pad($support->distribution_id, 2, '0', STR_PAD_LEFT); // Pads Distribution ID to 2 digits
        $supportId = str_pad($support->id, 3, '0', STR_PAD_LEFT); // Pads Support ID to 3 digits
        $uuidPart = strtoupper(Str::uuid()); // Generates a unique UUID
        $shortUuid = substr($uuidPart, 0, 8); // Extracts the first 8 characters of the UUID
        
        $support->unique_code = "SUP-{$distributionId}-{$supportId}-{$shortUuid}"; // Combines all parts
        $support->save();
    
    }

    /**
     * Handle the Support "updated" event.
     */
    public function updated(Support $support): void
    {
        //
    }

    /**
     * Handle the Support "deleted" event.
     */
    public function deleted(Support $support): void
    {
        //
    }

    /**
     * Handle the Support "restored" event.
     */
    public function restored(Support $support): void
    {
        //
    }

    /**
     * Handle the Support "force deleted" event.
     */
    public function forceDeleted(Support $support): void
    {
        //
    }
}
