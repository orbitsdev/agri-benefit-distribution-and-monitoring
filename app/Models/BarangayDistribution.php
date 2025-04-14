<?php

namespace App\Models;

use App\Models\Barangay;
use App\Models\Beneficiary;
use App\Models\Distribution;
use Illuminate\Database\Eloquent\Model;

class BarangayDistribution extends Model
{


    public function distribution()
    {
        return $this->belongsTo(Distribution::class);
    }

    public function barangay(){
        return $this->belongsTo(Barangay::class);
    }

    // has many beneficiaries
    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }
}
