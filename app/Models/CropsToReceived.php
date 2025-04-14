<?php

namespace App\Models;

use App\Models\Crop;
use App\Models\Beneficiary;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Observers\CropsToReceivedObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
#[ObservedBy([CropsToReceivedObserver::class])]
class CropsToReceived extends Model
{
    use HasFactory;




    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
