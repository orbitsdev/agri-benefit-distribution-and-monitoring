<?php

namespace App\Filament\Barangay\Resources\SupportRoleResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Barangay\Resources\SupportRoleResource;

class ManageSupportRoles extends ManageRecords
{
    protected static string $resource = SupportRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make() ->mutateFormDataUsing(function (array $data): array {
                $data['barangay_id'] = Auth::user()->barangay_id;

                   return $data;
               }),
        ];
    }
}
