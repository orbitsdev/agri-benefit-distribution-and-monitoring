<?php

namespace App\Filament\Barangay\Resources\ImportFailureResource\Pages;

use App\Filament\Barangay\Resources\ImportFailureResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewImportFailure extends ViewRecord
{
    protected static string $resource = ImportFailureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
