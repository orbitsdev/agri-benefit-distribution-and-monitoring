<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use Illuminate\Http\Request;
use App\Exports\SupportExport;
use App\Exports\BeneficiaryExport;
use App\Exports\SystemUsersExport;
use App\Exports\TransactionsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DistributionItemExport;
use App\Exports\BarangayDistributionExport;

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

public function exportBeneficiaries($distribution, $filter)
{
    // If a specific distribution ID is provided, fetch its title; otherwise, use "all"
    if ($distribution !== 'all') {
        $distributionModel = Distribution::find($distribution);
        $distributionTitle = $distributionModel ? str_replace(' ', '_', $distributionModel->title) : $distribution;
    } else {
        $distributionTitle = 'all';
    }

    $filename = 'Beneficiaries_' . $distributionTitle . '_' . $filter . '_' . now()->format('Y-m-d') . '.xlsx';
    return Excel::download(new BeneficiaryExport($distribution, $filter), $filename);
}

public function exportDistributionItems($distribution)
    {
        // If a specific distribution ID is provided, fetch its title; otherwise, use 'all'
        if ($distribution !== 'all') {
            $distributionModel = Distribution::find($distribution);
            $distributionTitle = $distributionModel ? str_replace(' ', '_', $distributionModel->title) : $distribution;
        } else {
            $distributionTitle = 'all';
        }

        $filename = 'DistributionItems_' . $distributionTitle . '_' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new DistributionItemExport($distribution), $filename);
    }

}
