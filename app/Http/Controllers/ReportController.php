<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\SupportExport;
use App\Exports\SystemUsersExport;
use App\Exports\TransactionsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BarangayDistributionExport;
use App\Models\Distribution;

class ReportController extends Controller
{

    public function exportBarangayDistributions(Request $request)
    {
        $barangayId = auth()->user()->barangay_id; // Assuming user belongs to a barangay
        $filename = 'Barangay_Distributions_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new BarangayDistributionExport($barangayId), $filename);
    }

    public function exportSystemUsers(Request $request)
    {
        $barangayId = auth()->user()->barangay_id; // Get the authenticated user's barangay
        $filename = 'System_Users_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new SystemUsersExport($barangayId), $filename);
    }
    public function exportSupports($record)
    {
        $filename = 'Supports_' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new SupportExport($record), $filename);
    }

    public function exportTransactions($record)
    {
        $distribution =  Distribution::find($record);
        $filename = $distribution->title.' Transactions-List' . now()->format('Y-m-d') . '.xlsx';
    return Excel::download(new TransactionsExport($record), $filename);
}

}
