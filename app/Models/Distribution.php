<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Support;
use App\Models\Barangay;
use App\Models\Transaction;
use App\Models\ImportFailure;
use App\Models\DistributionItem;
use App\Observers\DistributionObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
#[ObservedBy([DistributionObserver::class])]
class Distribution extends Model
{

    public const STATUS_PLANNED = 'Planned';
    public const STATUS_ONGOING = 'Ongoing';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_CANCELED = 'Canceled';

    public const STATUS_OPTIONS = [
        self::STATUS_PLANNED => 'Planned',
        self::STATUS_ONGOING => 'Ongoing',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELED => 'Canceled',
    ];

    protected function casts():array{
        return [
            'is_locked'=> 'boolean',
            'distribution_date' => 'datetime', // ✅ Ensures it's a Carbon instance

        ];

    }

    public function barangay(){
        return $this->belongsTo(Barangay::class);
    }

    public function distributionItems(){
        return $this->hasMany(DistributionItem::class);
    }

    // has many import failures
    public function importFailures(){
        return $this->hasMany(ImportFailure::class);
    }

    // has many supports

    public function supports()
    {
        return $this->hasMany(Support::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    //scope by barangay
    public function scopeByBarangay($query, $barangay_id){
        return $query->where('barangay_id', $barangay_id);
    }

    public function scopeNotCanceled($query)
    {
        return $query->where('status', '!=', self::STATUS_CANCELED);
    }

    public function scopePlanned($query)
    {
        return $query->where('status', self::STATUS_PLANNED);
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', self::STATUS_ONGOING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', self::STATUS_CANCELED);
    }
    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }


    public function scopeOngoingOrCompleted($query)
{
    return $query->whereIn('status', [self::STATUS_ONGOING, self::STATUS_COMPLETED]);
}



// public function getProgressPercentageAttribute()
// {
//     // Get total number of beneficiaries
//     $totalBeneficiaries = $this->distributionItems()
//         ->withCount('beneficiaries')
//         ->get()
//         ->sum('beneficiaries_count');

//     // Get total number of claimed beneficiaries
//     $claimedBeneficiaries = $this->distributionItems()
//         ->withCount(['beneficiaries as claimed_count' => function ($query) {
//             $query->where('status', 'Claimed');
//         }])
//         ->get()
//         ->sum('claimed_count');

//     // Avoid division by zero
//     if ($totalBeneficiaries === 0) {
//         return 0;
//     }

//     // Calculate the percentage
//     return round(($claimedBeneficiaries / $totalBeneficiaries) * 100, 2);
// }



public function getTotalBeneficiariesAttribute()
{
    return $this->distributionItems()
        ->withCount('beneficiaries')
        ->get()
        ->sum('beneficiaries_count');
}

public function getClaimedBeneficiariesAttribute()
{
    return $this->distributionItems()
        ->withCount(['beneficiaries as claimed_count' => function ($query) {
            $query->where('status', 'Claimed');
        }])
        ->get()
        ->sum('claimed_count');
}

public function getProgressPercentageAttribute()
{
    $total = $this->total_beneficiaries;
    $claimed = $this->claimed_beneficiaries;

    if ($total === 0) {
        return 0;
    }

    return round(($claimed / $total) * 100, 2);
}

public function hasUnlockedItems(): bool
{
    return $this->distributionItems()->where('is_locked', false)->exists();
}


}
