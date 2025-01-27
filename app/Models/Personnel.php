<?php

namespace App\Models;

use App\Models\User;
use App\Models\Support;
use App\Models\Barangay;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{

    // belongs to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // belongs to barangay


    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }



    //scope for active 
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    //scope for inactive
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }



    public function supports()
    {
        return $this->hasMany(Support::class);
    }
    public function scopeByBarangay($query, $barangay_id)
    {
        return $query->where('barangay_id', $barangay_id);
    }

    //scope barangay

    public function scopeNotRegisteredInSameDistributionAndBarangay($query, $distributionId, $barangayId)
    {
        return $query->whereDoesntHave('supports', function ($query) use ($distributionId, $barangayId) {
            $query->where('distribution_id', $distributionId)
                  ->where('barangay_id', $barangayId);
        });
    }
    

    public function scopeNotRegisteredInSameDistribution($query,$distributionId)
    {
        return $query->whereDoesntHave('supports', function ($query) use ($distributionId) {
            $query->where('distribution_id', '!=',$distributionId);
        });
    }

    //scope by barangay
   
}
