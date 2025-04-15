<?php

namespace App\Models;

use App\Models\User;
use App\Models\Support;
use App\Models\Barangay;
use App\Models\Beneficiary;
use App\Models\Distribution;
use App\Models\CropsToReceived;
use App\Models\BarangayDistribution;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model implements HasMedia
{
    use InteractsWithMedia;



    protected $casts = [
        'barangay_details' => 'array',
        'distribution_details' => 'array',
        'barangay_distribution_details' => 'array',
        'beneficiary_details' => 'array',
        'crops_details' => 'array',
        'recorder_details' => 'array',
        'performed_at' => 'datetime',
    ];

    // Relationships
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function cropsToReceive()
    {
        return $this->belongsTo(CropsToReceived::class, 'crops_to_received_id');
    }

    public function distribution()
    {
        return $this->belongsTo(Distribution::class);
    }

    public function barangayDistribution()
    {
        return $this->belongsTo(BarangayDistribution::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    // Media handling
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();

    }
    public function getCoverUrlAttribute(): ?string
    {
      return self::getImage();
    }

    public function getImage()
    {
        if ($this->hasMedia('image')) { // ✅ Ensure it's fetching from the correct collection
            return $this->getFirstMediaUrl('image');
        }

        return asset('images/placeholder-image.jpg'); // Default placeholder
    }

    // Scopes
    public function scopeByBarangay($query, $barangay_id)
    {
        return $query->whereHas('barangayDistribution', function($query) use ($barangay_id) {
            $query->where('barangay_id', $barangay_id);
        });
    }

    public function scopeByDistribution($query, $distribution_id)
    {
        return $query->where('distribution_id', $distribution_id);
    }

    public function scopeByBarangayDistribution($query, $barangay_distribution_id)
    {
        return $query->where('barangay_distribution_id', $barangay_distribution_id);
    }

    // Helper methods
    public static function recordClaim($beneficiary, $action = 'Claimed')
    {
        $user = Auth::user();
        $cropsToReceive = $beneficiary->cropsToReceive;
        $barangayDistribution = $beneficiary->barangayDistribution;
        $distribution = $barangayDistribution->distribution;
        $barangay = $barangayDistribution->barangay;

        return self::create([
            'barangay_id' => $barangay->id,
            'distribution_id' => $distribution->id,
            'barangay_distribution_id' => $barangayDistribution->id,
            'beneficiary_id' => $beneficiary->id,
            'crops_to_received_id' => $cropsToReceive->id,
            'barangay_details' => [
                'id' => $barangay->id,
                'name' => $barangay->name,
                'code' => $barangay->code,
                'municipality' => $barangay->municipality,
                'province' => $barangay->province,
            ],
            'distribution_details' => [
                'id' => $distribution->id,
                'title' => $distribution->title,
                'description' => $distribution->description,
                'distribution_date' => $distribution->distribution_date,
                'is_disbursed' => $distribution->is_disbursed,
                'is_completed' => $distribution->is_completed,
            ],
            'barangay_distribution_details' => [
                'id' => $barangayDistribution->id,
                'location' => $barangayDistribution->location,
                'distribution_date' => $barangayDistribution->distribution_date,
            ],
            'beneficiary_details' => [
                'id' => $beneficiary->id,
                'rsbsa_no' => $beneficiary->rsbsa_no,
                'first_name' => $beneficiary->first_name,
                'middle_name' => $beneficiary->middle_name,
                'last_name' => $beneficiary->last_name,
                'ext_name' => $beneficiary->ext_name,
                'gender' => $beneficiary->gender,
                'contact_num' => $beneficiary->contact_num,
                'email' => $beneficiary->email,
                'farmer_address' => $beneficiary->farmer_address,
                'farmer_address_mun' => $beneficiary->farmer_address_mun,
                'farmer_address_prv' => $beneficiary->farmer_address_prv,
            ],
            'crops_details' => [
                'id' => $cropsToReceive->id,
                'crop_name' => $cropsToReceive->crop->name,
                'unique_code' => $cropsToReceive->unique_code,
                'is_claimed' => $cropsToReceive->is_claimed,
                'date_claimed' => $cropsToReceive->date_claimed,
            ],
            'recorder_details' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'Unknown',
            ] : null,
            'action' => $action,
            'performed_at' => now(),
        ]);
    }
}
