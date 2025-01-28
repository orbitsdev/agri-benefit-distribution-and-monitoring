<?php

namespace App\Filament\Barangay\Resources\FailedJobResource\Pages;

use App\Filament\Barangay\Resources\FailedJobResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFailedJobs extends ListRecords
{
    protected static string $resource = FailedJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
