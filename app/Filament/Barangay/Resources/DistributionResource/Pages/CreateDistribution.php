<?php

namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Barangay\Resources\DistributionResource;

class CreateDistribution extends CreateRecord
{
    protected static string $resource = DistributionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
{
    $barangayId  = Auth::user()->barangay_id;

    $data['barangay_id'] = $barangayId;


    return $data;
}
}
