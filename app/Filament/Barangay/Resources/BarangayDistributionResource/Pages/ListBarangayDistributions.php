<?php

namespace App\Filament\Barangay\Resources\BarangayDistributionResource\Pages;

use App\Filament\Barangay\Resources\BarangayDistributionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarangayDistributions extends ListRecords
{
    protected static string $resource = BarangayDistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
