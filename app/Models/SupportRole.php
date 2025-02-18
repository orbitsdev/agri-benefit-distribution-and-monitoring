<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportRole extends Model
{


    public function scopeActive($query){
        return $query->where('status', true);
    }


    
}
