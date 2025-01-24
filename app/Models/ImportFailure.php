<?php

namespace App\Models;

use App\Models\Distribution;
use Illuminate\Database\Eloquent\Model;

class ImportFailure extends Model
{
    
   

    public function distribution(){
        return $this->belongsTo(Distribution::class);
    }
}
