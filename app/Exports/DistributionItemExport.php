<?php

namespace App\Exports;

use App\Models\DistributionItem;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DistributionItemExport implements FromView
{
    protected $distribution;

    // Accept the distribution parameter ('all' means export all)
    public function __construct($distribution = 'all')
    {
        $this->distribution = $distribution;
    }

    public function view(): View
    {
        $query = DistributionItem::with(['item', 'distribution']);

        // If a specific distribution is provided, filter by it
        if ($this->distribution !== 'all') {
            $query->where('distribution_id', $this->distribution);
        }

        $distributionItems = $query->get();

        return view('exports.distribution_items', compact('distributionItems'));
    }
}
