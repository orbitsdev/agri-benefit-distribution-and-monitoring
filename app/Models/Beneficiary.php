<?php

namespace App\Models;

use App\Models\Transaction;
use App\Models\DistributionItem;
use App\Observers\BeneficiaryObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([BeneficiaryObserver::class])]
class Beneficiary extends Model
{
    // belongsTo relationship with DistributionItem

    //create static  for status
    public const CLAIMED = 'Claimed';
    public const UN_CLAIMED = 'Unclaimed';

    public const STATUS_OPTIONS = [
        self::CLAIMED => 'Claimed',
        self::UN_CLAIMED => 'Unclaimed',
    ];

    // Beneficiary model
    public function distributionItem()
    {
        return $this->belongsTo(DistributionItem::class, 'distribution_item_id');
    }


    // has many transactions

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Scope for claimed beneficiaries
    public function scopeClaimed($query)
    {
        return $query->where('status', self::CLAIMED);
    }

    // Scope for unclaimed beneficiaries
    public function scopeUnclaimed($query)
    {
        return $query->where('status', self::UN_CLAIMED);
    }

    // Scope for beneficiaries whose parent distribution is NOT canceled
    public function scopeWhereDistributionNotCanceled($query)
    {
        return $query->whereHas('distributionItem.distribution', function ($query) {
            $query->where('status', '!=', Distribution::STATUS_CANCELED);
        });
    }




    // Scope for beneficiaries where the distribution belongs to a specific barangay
    public function scopeByBarangay($query, $barangayId)
    {
        return $query->whereHas('distributionItem.distribution', function ($query) use ($barangayId) {
            $query->where('barangay_id', $barangayId);
        });
    }


}
