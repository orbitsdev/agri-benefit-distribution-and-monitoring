<?php

namespace App\Filament\Barangay\Resources\FailedJobResource\Pages;

use App\Filament\Barangay\Resources\FailedJobResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFailedJob extends EditRecord
{
    protected static string $resource = FailedJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
