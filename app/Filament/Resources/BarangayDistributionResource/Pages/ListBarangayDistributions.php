<?php

namespace App\Filament\Resources\BarangayDistributionResource\Pages;

use App\Filament\Resources\BarangayDistributionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarangayDistributions extends ListRecords
{
    protected static string $resource = BarangayDistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
