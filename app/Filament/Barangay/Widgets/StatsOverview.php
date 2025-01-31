<?php

namespace App\Filament\Barangay\Widgets;

use App\Models\User;
use App\Models\Beneficiary;
use App\Models\Distribution;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{

    protected function getStats(): array
    {
        $barangayId = Auth::user()->barangay_id;

        // Distribution Stats
        $totalDistributions = Distribution::byBarangay($barangayId)->notCanceled()->count();
        $plannedDistributions = Distribution::byBarangay($barangayId)->planned()->count();
        $ongoingDistributions = Distribution::byBarangay($barangayId)->ongoing()->count();
        $completedDistributions = Distribution::byBarangay($barangayId)->completed()->count();
        $canceledDistributions = Distribution::byBarangay($barangayId)->canceled()->count();

        // // Beneficiary Stats (Filtered by Barangay)
        // $totalBeneficiaries = Beneficiary::byBarangay($barangayId)->whereDistributionNotCanceled()->count();
        // $claimedBeneficiaries = Beneficiary::byBarangay($barangayId)->whereDistributionNotCanceled()->claimed()->count();
        // $unclaimedBeneficiaries = Beneficiary::byBarangay($barangayId)->whereDistributionNotCanceled()->unclaimed()->count();

        // User Stats (Filtered by Barangay)
        $totalUsers = User::byBarangay($barangayId)->isMember()->count();
        $activeUsers = User::byBarangay($barangayId)->isMember()->active()->count();
        $inactiveUsers = User::byBarangay($barangayId)->isMember()->inactive()->count();

        return [

            Stat::make('Total Distributions', $totalDistributions)
            ->description("Planned: $plannedDistributions | Ongoing: $ongoingDistributions | Completed: $completedDistributions | Canceled: $canceledDistributions")
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->chart(
                Distribution::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('barangay_id', $barangayId)
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->pluck('count') // Fetch counts as data points
                    ->toArray()
            )
            ->icon('heroicon-o-arrow-down-tray')
            ->color('primary')
            ->extraAttributes([
                "class" => "cursor-pointer",
                "wire:click" => "goto('reports.barangay-distributions')",
            ]),



                // Stat::make('Total Beneficiaries', $totalBeneficiaries)
                // ->description("Claimed: $claimedBeneficiaries | Unclaimed: $unclaimedBeneficiaries")
                // // ->descriptionIcon('heroicon-m-users')
                // ,

                Stat::make('System Users', $totalUsers)
                ->description("Active: $activeUsers | Inactive: $inactiveUsers")
                ->descriptionIcon('heroicon-m-user-group')
                ->chart(
                    User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                        ->where('barangay_id', $barangayId)
                        ->groupBy('date')
                        ->orderBy('date', 'asc')
                        ->pluck('count') // Fetch counts as data points
                        ->toArray()
                )
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->extraAttributes([
                    "class" => "cursor-pointer",
                    "wire:click" => "goto('reports.system-users')",
                ]),


        ];
    }

    public function goto($routeName)
    {
        return redirect()->route($routeName);
    }
}
