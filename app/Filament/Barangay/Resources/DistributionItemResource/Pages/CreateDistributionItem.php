<?php

namespace App\Filament\Barangay\Resources\DistributionItemResource\Pages;

use App\Filament\Barangay\Resources\DistributionItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDistributionItem extends CreateRecord
{
    protected static string $resource = DistributionItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
