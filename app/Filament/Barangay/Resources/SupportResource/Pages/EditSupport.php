<?php

namespace App\Filament\Barangay\Resources\SupportResource\Pages;

use App\Filament\Barangay\Resources\SupportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupport extends EditRecord
{
    protected static string $resource = SupportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
