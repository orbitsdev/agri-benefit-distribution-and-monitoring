<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Barangay;
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

    // has many items
    // public function items(){
    //     return $this->hasMany(Item::class);
    // }
}
