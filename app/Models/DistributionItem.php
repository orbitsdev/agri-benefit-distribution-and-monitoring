<?php

namespace App\Models;


use App\Models\Item;
use App\Models\Beneficiary;
use App\Models\Transaction;
use App\Models\Distribution;
use Illuminate\Database\Eloquent\Model;

class DistributionItem extends Model
{
    
    // create static status and options similar to Distribution model
    public const CLAIMED = 'Claimed';
    public const UN_CLAIMED = 'Unclaimed';

    

    public function distribution(){
        return $this->belongsTo(Distribution::class);
    }
    public function item(){
        return $this->belongsTo(Item::class);
    }

    // hasMany relationship with Beneficiary
    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }

    // scope count for total beneficiaries claimed and unclaimed
    
    public function scopeTotalClaimed($query)
    {
        return $query->whereHas('beneficiaries', function ($query) {
            $query->where('status', self::CLAIMED);
        });
    }

    public function scopeTotalUnclaimed($query)
    {
        return $query->whereHas('beneficiaries', function ($query) {
            $query->where('status', self::UN_CLAIMED);
        });
    }

    public function scopeTotalClaimedQuantity($query)
    {
        return $query->whereHas('beneficiaries', function ($query) {
            $query->where('status', self::CLAIMED);
        })->sum('quantity');
    }
    
    public function scopeTotalUnclaimedQuantity($query)
    {
        return $query->whereHas('beneficiaries', function ($query) {
            $query->where('status', self::UN_CLAIMED);
        })->sum('quantity');
    }

    public function scopeTotalClaimedBeneficiaries($query)
    {
        return $query->whereHas('beneficiaries', function ($query) {
            $query->where('status', self::CLAIMED);
        })->count();
    }

    public function scopeTotalUnclaimedBeneficiaries($query)
    {
        return $query->whereHas('beneficiaries', function ($query) {
            $query->where('status', self::UN_CLAIMED);
        })->count();
    }

    public function getTotalBeneficiaries()
    {
        return $this->beneficiaries()->count();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
