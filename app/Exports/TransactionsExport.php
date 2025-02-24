<?php

namespace App\Exports;

use App\Models\Transaction;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class TransactionsExport implements FromView
{
    protected $distributionId;

    // Inject the distribution ID (or any filter parameter)
    public function __construct($distributionId)
    {
        $this->distributionId = $distributionId;
    }

    public function view(): View
    {
        // Retrieve transactions for the given distribution ID
        $transactions = Transaction::with([
            'beneficiary',
            'distribution',
            'barangay',
            'support',
            'admin'
        ])
        ->where('distribution_id', $this->distributionId)
        ->get();

        return view('exports.transactions', compact('transactions'));
    }
}
