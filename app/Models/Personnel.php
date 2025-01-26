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
    return $query->where('status', 'active');

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


//scope function where doesnt have the same barnaggau personne l




}
