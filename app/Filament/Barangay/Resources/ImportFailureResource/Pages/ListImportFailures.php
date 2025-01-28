<?php

namespace App\Filament\Barangay\Resources\ImportFailureResource\Pages;

use App\Filament\Barangay\Resources\ImportFailureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportFailures extends ListRecords
{
    protected static string $resource = ImportFailureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
