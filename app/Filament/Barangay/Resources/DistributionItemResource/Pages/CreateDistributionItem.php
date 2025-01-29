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


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // $data['user_id'] = auth()->id();

        // dd('test');
     
        return $data;
    }
}
