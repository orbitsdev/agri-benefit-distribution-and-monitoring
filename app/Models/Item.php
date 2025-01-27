<?php

namespace App\Models;

use App\Models\Barangay;
use App\Models\DistributionItem;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{   
    
    // belongs to barangay
    public function barangay(){
        return $this->belongsTo(Barangay::class);
    }

    public function distributionItems(){
        return $this->hasMany(DistributionItem::class);
    }

    //scope accourding to barangay
    public function scopeByBarangay($query, $barangay_id){
        return $query->where('barangay_id', $barangay_id);
    }
    

    //scope active
    public function scopeActive($query){
        return $query->where('is_active', true);
    }


   
    
}
