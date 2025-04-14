<?php

namespace App\Models;

use App\Models\Distribution;
use App\Models\CropsToReceived;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Crop extends Model
{
    use HasFactory;
  

    public function distribution()
    {
        return $this->belongsTo(Distribution::class);
    }

    public function cropsToReceive()
    {
        return $this->hasMany(CropsToReceived::class);
    }
}
