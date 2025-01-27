<?php

namespace App\Models;

use App\Models\Personnel;
use App\Models\Transaction;
use App\Models\Distribution;
use App\Observers\SupportObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([SupportObserver::class])]
class Support extends Model
{
    

    // belongs to personnel

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    // belonf to destribution

    public function distribution()
    {
        return $this->belongsTo(Distribution::class);
    }


    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    //scope where not equal to the same distribution and personnel

    public function scopeNotEqual($query, $distributionId, $personnelId)
    {
        return $query->where('distribution_id', '!=', $distributionId)
            ->where('personnel_id', '!=', $personnelId);
    }

    public function hasTransactions($barangayId): bool
{
    return $this->transactions()->where('barangay_id', $barangayId)->exists();
}


}
