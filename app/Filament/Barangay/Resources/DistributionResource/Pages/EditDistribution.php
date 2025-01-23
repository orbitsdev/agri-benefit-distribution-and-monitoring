<?php

namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use App\Filament\Barangay\Resources\DistributionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDistribution extends EditRecord
{
    protected static string $resource = DistributionResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
