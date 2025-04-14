<?php

namespace App\Exports;

use App\Models\Crop;
use App\Models\Distribution;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CropExport implements FromView
{
    protected $distribution;

    public function __construct($distribution = 'all')
    {
        $this->distribution = $distribution;
    }

    public function view(): View
    {
        // Get all crops used in this distribution
        $query = Crop::query();

        // If a specific distribution is provided, filter by it
        if ($this->distribution !== 'all') {
            $distribution = Distribution::find($this->distribution);

            if ($distribution) {
                $query->where('distribution_id', $distribution->id);
            }
        }

        $crops = $query->get();

        return view('exports.crops', compact('crops'));
    }
}
