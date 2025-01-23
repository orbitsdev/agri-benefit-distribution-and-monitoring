<?php

namespace App\Models;

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


}
