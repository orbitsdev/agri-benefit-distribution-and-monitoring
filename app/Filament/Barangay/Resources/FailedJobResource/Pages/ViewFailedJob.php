<?php

namespace App\Filament\Barangay\Resources\FailedJobResource\Pages;

use App\Filament\Barangay\Resources\FailedJobResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFailedJob extends ViewRecord
{
    protected static string $resource = FailedJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
