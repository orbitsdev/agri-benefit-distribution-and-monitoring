<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Personnel;
use App\Models\Transaction;
use App\Models\Distribution;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

class Barangay extends Model implements HasMedia
{
    use InteractsWithMedia;
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();

    }

    public function getImage()
    {
        if ($this->hasMedia()) {
            return $this->getFirstMediaUrl();
        }

        return asset('images/placeholder-image.jpg');
    }

    // has many distributions
    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }


    // has many personnel
    public function personnel()
    {
        return $this->hasMany(Personnel::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);

    
    }

    // has many item
    public function items()
    {
        return $this->hasMany(Item::class);
    }

}
