<?php

namespace App\Models;

use App\Models\Transaction;
use App\Models\DistributionItem;
use Illuminate\Database\Eloquent\Model;

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

    public function distributionItem()
    {
        return $this->belongsTo(DistributionItem::class);
    }

    // has many transactions

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
