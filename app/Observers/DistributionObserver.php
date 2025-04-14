<?php

namespace App\Observers;

use App\Models\Barangay;
use Illuminate\Support\Str;
use App\Models\Distribution;
use App\Models\BarangayDistribution;

class DistributionObserver
{
    /**
     * Handle the Distribution "created" event.
     */
    public function created(Distribution $distribution): void
    {
    //     $barangayCode = strtoupper(substr($distribution->barangay->name, 0, 3)); // First 3 letters of barangay name

    // $uuidPart = strtoupper(Str::uuid()); // Generate a full UUID
    // $shortUuidPart = substr($uuidPart, 0, 8); // Use the first 8 characters for brevity

    // $distribution->code = "BRGY-{$barangayCode}-{$shortUuidPart}";
    // $distribution->save(); // Save the updated code

    $barangays = Barangay::all(); // ✅ only active barangays

$payload = $barangays->map(function ($barangay) use ($distribution) {
    return [
        'distribution_id' => $distribution->id,
        'barangay_id' => $barangay->id,
        'distribution_date' => $distribution->distribution_date,
        'location' => $barangay->location,
        'is_disbursed' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ];
})->toArray();

BarangayDistribution::insert($payload); // ✅ bulk insert for performance

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
