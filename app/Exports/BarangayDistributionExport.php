<?php

namespace App\Exports;

use App\Models\Distribution;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BarangayDistributionExport implements FromView
{
    protected $barangayId;

    public function __construct($barangayId)
    {
        $this->barangayId = $barangayId;
    }

    public function view(): View
    {
        // Fetch distributions with related data
        $distributions = Distribution::where('barangay_id', $this->barangayId)
        ->with([
            'distributionItems.item',
            'distributionItems.beneficiaries',
            'supports.personnel.user'
        ])
           ->latest()
            ->get();

        return view('exports.barangay-distributions', [
            'distributions' => $distributions,
        ]);
    }
}
