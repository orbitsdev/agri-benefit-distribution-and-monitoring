<?php

namespace App\Exports;

use App\Models\Crop;
use App\Models\BarangayDistribution;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class BarangayCropExport implements FromView
{
    protected $barangayDistributionId;

    public function __construct($barangayDistributionId)
    {
        $this->barangayDistributionId = $barangayDistributionId;
    }

    public function view(): View
    {
        $barangayDistribution = BarangayDistribution::with(['distribution', 'barangay'])->find($this->barangayDistributionId);

        // Get crops through the cropsToReceive relationship
        $crops = Crop::whereHas('cropsToReceive.beneficiary', function ($query) use ($barangayDistribution) {
            $query->where('barangay_distribution_id', $barangayDistribution->id);
        })->with('distribution')->get();

        return view('exports.crops', [
            'crops' => $crops,
            'barangayDistribution' => $barangayDistribution
        ]);
    }
}