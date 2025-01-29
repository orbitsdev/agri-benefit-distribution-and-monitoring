<?php

namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use App\Filament\Barangay\Resources\DistributionResource;
use App\Models\Distribution;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDistribution extends ViewRecord
{
    protected static string $resource = DistributionResource::class;

    

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
