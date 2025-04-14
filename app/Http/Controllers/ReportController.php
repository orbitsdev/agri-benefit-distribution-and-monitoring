<?php

namespace App\Http\Controllers;

use App\Exports\CropExport;
use App\Models\Distribution;
use Illuminate\Http\Request;
use App\Exports\SupportExport;
use App\Exports\BeneficiaryExport;
use App\Exports\SystemUsersExport;
use App\Exports\TransactionsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DistributionItemExport;
use App\Exports\BarangayDistributionExport;
use App\Exports\BarangayCropExport;
use App\Exports\BarangayBeneficiaryExport;
use App\Exports\BarangayTransactionsExport;

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
public function exportCrops($distribution)
{
    // If a specific distribution ID is provided, fetch its title; otherwise, use 'all'
    if ($distribution !== 'all') {
        $distributionModel = Distribution::find($distribution);
        $distributionTitle = $distributionModel ? str_replace(' ', '_', $distributionModel->title) : $distribution;
    } else {
        $distributionTitle = 'all';
    }

    $filename = 'Crops_' . $distributionTitle . '_' . now()->format('Y-m-d') . '.xlsx';
    return Excel::download(new CropExport($distribution), $filename);
}
public function exportBarangayTransactions($record)
{
    // Security check - ensure the barangay user can only access their own data
    $barangayDistribution = \App\Models\BarangayDistribution::find($record);
    if (!$barangayDistribution || $barangayDistribution->barangay_id !== auth()->user()->barangay_id) {
        abort(403, 'Unauthorized access to barangay distribution data');
    }

    $filename = $barangayDistribution->distribution->title . ' Barangay Transactions-List ' . now()->format('Y-m-d') . '.xlsx';
    return Excel::download(new BarangayTransactionsExport($record), $filename);
}

public function exportBarangayBeneficiaries($barangayDistribution, $filter)
{
    // Security check - ensure the barangay user can only access their own data
    $barangayDist = \App\Models\BarangayDistribution::find($barangayDistribution);
    if (!$barangayDist || $barangayDist->barangay_id !== auth()->user()->barangay_id) {
        abort(403, 'Unauthorized access to barangay distribution data');
    }

    $distributionTitle = str_replace(' ', '_', $barangayDist->distribution->title . '_' . $barangayDist->barangay->name);
    $filename = 'Barangay_Beneficiaries_' . $distributionTitle . '_' . $filter . '_' . now()->format('Y-m-d') . '.xlsx';
    return Excel::download(new BarangayBeneficiaryExport($barangayDistribution, $filter), $filename);
}

public function exportBarangayCrops($barangayDistribution)
{
    // Security check - ensure the barangay user can only access their own data
    $barangayDist = \App\Models\BarangayDistribution::find($barangayDistribution);
    if (!$barangayDist || $barangayDist->barangay_id !== auth()->user()->barangay_id) {
        abort(403, 'Unauthorized access to barangay distribution data');
    }

    $distributionTitle = str_replace(' ', '_', $barangayDist->distribution->title . '_' . $barangayDist->barangay->name);
    $filename = 'Barangay_Crops_' . $distributionTitle . '_' . now()->format('Y-m-d') . '.xlsx';
    return Excel::download(new BarangayCropExport($barangayDistribution), $filename);
}
}
