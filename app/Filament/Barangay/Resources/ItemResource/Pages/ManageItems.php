<?php

namespace App\Filament\Barangay\Resources\ItemResource\Pages;

use App\Filament\Barangay\Resources\ItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageItems extends ManageRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
