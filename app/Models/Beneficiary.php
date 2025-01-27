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
}
