<?php

namespace App\Exports;

use App\Models\Beneficiary;
use App\Models\Distribution;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BeneficiaryExport implements FromView
{
    protected $distribution;
    protected $filter; // Accepts 'all', 'claimed', or 'unclaimed'

    public function __construct($distribution = 'all', $filter = 'all')
    {
        $this->distribution = $distribution;
        $this->filter = $filter;
    }

    public function view(): View
    {
        // Eager load related data
        $query = Beneficiary::with([
            'barangayDistribution.distribution',
            'barangayDistribution.barangay',
            'cropsToReceive.crop'
        ]);

        // Filter by distribution if a specific ID is provided
        if ($this->distribution !== 'all') {
            $query->whereHas('barangayDistribution', function ($q) {
                $q->where('distribution_id', $this->distribution);
            });
        }

        // Apply status filter if not 'all'
        if ($this->filter === 'claimed') {
            $query->whereHas('cropsToReceive', function ($q) {
                $q->where('is_claimed', true);
            });
        } elseif ($this->filter === 'unclaimed') {
            $query->whereHas('cropsToReceive', function ($q) {
                $q->where('is_claimed', false);
            });
        }

        $beneficiaries = $query->get();

        // This view should follow your Excel layout
        return view('exports.beneficiaries', compact('beneficiaries'));
    }
}
