<?php

namespace App\Filament\Barangay\Resources\PersonnelResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Barangay\Resources\PersonnelResource;

class CreatePersonnel extends CreateRecord
{
    protected static string $resource = PersonnelResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['barangay_id'] = Auth::user()->barangay_id;
 
    return $data;
}
}
