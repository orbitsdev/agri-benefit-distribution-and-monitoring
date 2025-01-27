<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Support;
use App\Models\Barangay;
use App\Models\Transaction;
use App\Models\ImportFailure;
use App\Models\DistributionItem;
use App\Observers\DistributionObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
#[ObservedBy([DistributionObserver::class])]
class Distribution extends Model
{

    public const STATUS_PLANNED = 'Planned';
    public const STATUS_ONGOING = 'Ongoing';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_CANCELED = 'Canceled';

    public const STATUS_OPTIONS = [
        self::STATUS_PLANNED => 'Planned',
        self::STATUS_ONGOING => 'Ongoing',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELED => 'Canceled',
    ];


    public function barangay(){
        return $this->belongsTo(Barangay::class);
    }

    public function distributionItems(){
        return $this->hasMany(DistributionItem::class);
    }

    // has many import failures
    public function importFailures(){
        return $this->hasMany(ImportFailure::class);
    }

    // has many supports

    public function supports()
    {
        return $this->hasMany(Support::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    //scope by barangay
    public function scopeByBarangay($query, $barangay_id){
        return $query->where('barangay_id', $barangay_id);
    }
    
}
