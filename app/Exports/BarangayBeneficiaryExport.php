<?php

namespace App\Exports;

use App\Models\Beneficiary;
use App\Models\BarangayDistribution;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class BarangayBeneficiaryExport implements FromView
{
    protected $barangayDistributionId;
    protected $filter;

    public function __construct($barangayDistributionId, $filter)
    {
        $this->barangayDistributionId = $barangayDistributionId;
        $this->filter = $filter;
    }

    public function view(): View
    {
        $query = Beneficiary::with(['cropsToReceive', 'barangayDistribution.barangay'])
            ->where('barangay_distribution_id', $this->barangayDistributionId);

        // Apply filter if specified
        if ($this->filter === 'claimed') {
            $query->whereHas('cropsToReceive', function ($query) {
                $query->where('is_claimed', true);
            });
        } elseif ($this->filter === 'unclaimed') {
            $query->whereHas('cropsToReceive', function ($query) {
                $query->where('is_claimed', false);
            });
        }

        $beneficiaries = $query->get();

        return view('exports.beneficiaries', [
            'beneficiaries' => $beneficiaries,
            'filter' => $this->filter
        ]);
    }
}
