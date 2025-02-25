<?php

namespace App\Exports;

use App\Models\Beneficiary;
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
        // Eager load related distribution and item details
        $query = Beneficiary::with([
            'distributionItem.distribution',
            'distributionItem.item'
        ]);

        // Filter by distribution if a specific ID is provided
        if ($this->distribution !== 'all') {
            $query->whereHas('distributionItem', function ($q) {
                $q->where('distribution_id', $this->distribution);
            });
        }

        // Apply status filter if not 'all'
        if ($this->filter === 'claimed') {
            $query->where('status', 'Claimed');
        } elseif ($this->filter === 'unclaimed') {
            $query->where('status', 'Unclaimed');
        }

        $beneficiaries = $query->get();

        // This view should follow your Excel layout (dates formatted with Carbon in the Blade view)
        return view('exports.beneficiaries', compact('beneficiaries'));
    }
}
