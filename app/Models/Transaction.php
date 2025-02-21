<?php

namespace App\Models;

use App\Models\User;
use App\Models\Support;
use App\Models\Barangay;
use App\Models\Beneficiary;
use App\Models\Distribution;
use App\Models\DistributionItem;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Transaction extends Model implements HasMedia
{
        use InteractsWithMedia;

        protected $casts = [
            'barangay_details' => 'array',
            'distribution_details' => 'array',
            'distribution_item_details' => 'array',
            'beneficiary_details' => 'array',
            'support_details' => 'array',
        ];



    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function distributionItem()
    {
        return $this->belongsTo(DistributionItem::class);
    }

    public function distribution()
    {
        return $this->belongsTo(Distribution::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function support()
    {
        return $this->belongsTo(Support::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }


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

    public function scopeByBarangay($query, $barangay_id){
        return $query->where('barangay_id', $barangay_id);
    }
    public function scopeByDistribution($query, $distribution_id){
        return $query->where('distribution_id', $distribution_id);
    }
}
