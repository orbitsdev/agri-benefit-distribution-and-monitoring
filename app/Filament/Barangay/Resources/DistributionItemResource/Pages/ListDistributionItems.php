<?php

namespace App\Filament\Barangay\Resources\DistributionItemResource\Pages;

use App\Filament\Barangay\Resources\DistributionItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDistributionItems extends ListRecords
{
    protected static string $resource = DistributionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
