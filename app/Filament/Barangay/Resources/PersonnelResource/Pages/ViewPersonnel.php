<?php

namespace App\Filament\Barangay\Resources\PersonnelResource\Pages;

use App\Filament\Barangay\Resources\PersonnelResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPersonnel extends ViewRecord
{
    protected static string $resource = PersonnelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
