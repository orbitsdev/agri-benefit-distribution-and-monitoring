<?php

namespace App\Filament\Barangay\Resources\ImportFailureResource\Pages;

use App\Filament\Barangay\Resources\ImportFailureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImportFailure extends EditRecord
{
    protected static string $resource = ImportFailureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
