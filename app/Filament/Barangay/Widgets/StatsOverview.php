<?php

namespace App\Filament\Barangay\Widgets;

use App\Models\User;
use App\Models\Beneficiary;
use App\Models\Transaction;
use App\Models\BarangayDistribution;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $barangayId = Auth::user()->barangay_id;
        
        // BarangayDistribution stats
        $totalBarangayDistributions = BarangayDistribution::where('barangay_id', $barangayId)->count();
        $activeBarangayDistributions = BarangayDistribution::where('barangay_id', $barangayId)
            ->where('is_disbursed', true)
            ->count();
            
        // Beneficiary stats
        $totalBeneficiaries = Beneficiary::whereHas('barangayDistribution', function($query) use ($barangayId) {
            $query->where('barangay_id', $barangayId);
        })->count();
        
        $claimedBeneficiaries = Beneficiary::whereHas('barangayDistribution', function($query) use ($barangayId) {
            $query->where('barangay_id', $barangayId);
        })->whereHas('cropsToReceive', function($query) {
            $query->where('is_claimed', true);
        })->count();
        
        // Transaction stats
        $totalTransactions = Transaction::whereHas('barangayDistribution', function($query) use ($barangayId) {
            $query->where('barangay_id', $barangayId);
        })->count();
        
        // Member stats
        $totalMembers = User::where('barangay_id', $barangayId)->where('role', User::MEMBER)->count();
        
        return [
            Stat::make('Barangay Distributions', $totalBarangayDistributions)
                ->description("Active: $activeBarangayDistributions")
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),
                
            Stat::make('Beneficiaries', $totalBeneficiaries)
                ->description("Claimed: $claimedBeneficiaries")
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
                
            Stat::make('Transactions', $totalTransactions)
                ->description("Total recorded transactions")
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),
                
            Stat::make('Team Members', $totalMembers)
                ->description("Barangay system users")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
    
    public function goto($routeName)
    {
        return redirect()->route($routeName);
    }
}
