<?php

namespace App\Filament\Barangay\Resources\SupportRoleResource\Pages;

use App\Filament\Barangay\Resources\SupportRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSupportRoles extends ManageRecords
{
    protected static string $resource = SupportRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
