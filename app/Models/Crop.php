<?php

namespace App\Models;

use App\Models\Distribution;
use App\Models\CropsToReceived;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Crop extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribution_id',
        'name',
        'original_stocks',
        'updated_stocks',
        'is_active'
    ];

    public function distribution()
    {
        return $this->belongsTo(Distribution::class);
    }

    public function cropsToReceive()
    {
        return $this->hasMany(CropsToReceived::class);
    }

    /**
     * Decrease the crop inventory by one unit
     *
     * @return bool
     */
    public function decreaseInventory()
    {
        // Check if we have stock available and don't go below zero
        if ($this->updated_stocks > 0) {
            // Ensure we don't decrease more than what was originally allocated
            $totalClaimed = $this->original_stocks - $this->updated_stocks;

            if ($totalClaimed < $this->original_stocks) {
                $this->updated_stocks -= 1;
                return $this->save();
            }
        }

        return false;
    }

    /**
     * Increase the crop inventory by one unit
     *
     * @return bool
     */
    public function increaseInventory()
    {
        // Don't allow stock to exceed original amount
        if ($this->updated_stocks < $this->original_stocks) {
            $this->updated_stocks += 1;
            return $this->save();
        }

        return false;
    }

    /**
     * Get the remaining stock percentage
     *
     * @return float
     */
    public function getRemainingPercentage()
    {
        if ($this->original_stocks <= 0) {
            return 0;
        }

        return round(($this->updated_stocks / $this->original_stocks) * 100, 2);
    }
}
