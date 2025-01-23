<?php

namespace App\Models;

use App\Models\DistributionItem;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    // belongsTo relationship with DistributionItem
    public function distributionItem()
    {
        return $this->belongsTo(DistributionItem::class);
    }
}
