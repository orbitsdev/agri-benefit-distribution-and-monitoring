<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BarangayDistributionExport;

class ReportController extends Controller
{

    public function exportBarangayDistributions(Request $request)
    {
        $barangayId = auth()->user()->barangay_id; // Assuming user belongs to a barangay
        $filename = 'Barangay_Distributions_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new BarangayDistributionExport($barangayId), $filename);
    }
}
