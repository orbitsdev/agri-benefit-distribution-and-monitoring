<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class BarangayTransactionsExport implements FromView
{
    protected $barangayDistributionId;

    public function __construct($barangayDistributionId)
    {
        $this->barangayDistributionId = $barangayDistributionId;
    }

    public function view(): View
    {
        // Retrieve transactions for the given barangay distribution ID
        $transactions = Transaction::with(['media'])
            ->where('barangay_distribution_id', $this->barangayDistributionId)
            ->orderBy('performed_at', 'desc')
            ->get();

        return view('exports.transactions', compact('transactions'));
    }
}
