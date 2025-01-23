<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
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
}
