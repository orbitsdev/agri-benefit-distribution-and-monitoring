<?php

namespace App\Observers;

use Illuminate\Support\Str;
use App\Models\Distribution;

class DistributionObserver
{
    /**
     * Handle the Distribution "created" event.
     */
    public function created(Distribution $distribution): void
    {
        $barangayCode = strtoupper(substr($distribution->barangay->name, 0, 3)); // First 3 letters of barangay name

    $uuidPart = strtoupper(Str::uuid()); // Generate a full UUID
    $shortUuidPart = substr($uuidPart, 0, 8); // Use the first 8 characters for brevity

    $distribution->code = "BRGY-{$barangayCode}-{$shortUuidPart}";
    $distribution->save(); // Save the updated code
    }

    /**
     * Handle the Distribution "updated" event.
     */
    public function updated(Distribution $distribution): void
    {
        //
    }

    /**
     * Handle the Distribution "deleted" event.
     */
    public function deleted(Distribution $distribution): void
    {
        //
    }

    /**
     * Handle the Distribution "restored" event.
     */
    public function restored(Distribution $distribution): void
    {
        //
    }

    /**
     * Handle the Distribution "force deleted" event.
     */
    public function forceDeleted(Distribution $distribution): void
    {
        //
    }
}
