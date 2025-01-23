<?php

namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use App\Filament\Barangay\Resources\DistributionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDistributions extends ListRecords
{
    protected static string $resource = DistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
