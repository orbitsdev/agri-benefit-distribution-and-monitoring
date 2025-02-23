<?php

namespace App\Exports;

use App\Models\Support;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class SupportExport implements FromView
{
    protected $distributionId;

    // Accept the distribution ID when creating an instance
    public function __construct($distributionId)
    {
        $this->distributionId = $distributionId;
    }

    public function view(): View
    {
        // Retrieve supports with the specific distribution ID
        $supports = Support::with(['personnel', 'distribution'])
            ->where('distribution_id', $this->distributionId)
            ->get();

        return view('exports.supports', compact('supports'));
    }
}
