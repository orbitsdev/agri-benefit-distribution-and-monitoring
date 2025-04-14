<?php

namespace App\Filament\Barangay\Resources\BarangayDistributionResource\Pages;

use App\Filament\Barangay\Resources\BarangayDistributionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBarangayDistribution extends EditRecord
{
    protected static string $resource = BarangayDistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
