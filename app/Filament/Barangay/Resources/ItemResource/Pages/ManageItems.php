<?php

namespace App\Filament\Barangay\Resources\ItemResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Barangay\Resources\ItemResource;

class ManageItems extends ManageRecords
{
    protected static string $resource = ItemResource::class;

    

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
