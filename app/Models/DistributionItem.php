<?php

namespace App\Models;


use App\Models\Item;
use App\Models\Distribution;
use Illuminate\Database\Eloquent\Model;

class DistributionItem extends Model
{
    

    public function distribution(){
        return $this->belongsTo(Distribution::class);
    }
    public function item(){
        return $this->belongsTo(Item::class);
    }
}
