<?php

namespace App\Models;

use App\Models\Barangay;
use Illuminate\Database\Eloquent\Model;

class SupportRole extends Model
{


    public function scopeActive($query){
        return $query->where('is_active', true);
    }


    public function barangay(){
        return $this->belongsTo(Barangay::class);
    }
    public function scopeByBarangay($query, $barangay_id){
        return $query->where('barangay_id', $barangay_id);
    }


}
