<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Barangay;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class Overview extends BaseWidget
{
    protected function getStats(): array
    {

        $totalAdmins = User::adminWithBarangay()->count();

        // Barangay Stats
        $totalBarangays = Barangay::count();
        return [
            Stat::make('Administrators', $totalAdmins)
                ->descriptionIcon('heroicon-m-shield-check')
                ->icon('heroicon-o-shield-check')
                ->color('secondary')
                ->extraAttributes([
                    "class" => "cursor-pointer",
                    "wire:click" => "goto('reports.admin-users')",
                ]),

            Stat::make('Total Barangays', $totalBarangays)
                ->descriptionIcon('heroicon-m-home')
                ->icon('heroicon-o-home')
                ->color('success')
                ->extraAttributes([
                    "class" => "cursor-pointer",
                    "wire:click" => "goto('reports.barangays')",
                ]),
        ];
    }
}
