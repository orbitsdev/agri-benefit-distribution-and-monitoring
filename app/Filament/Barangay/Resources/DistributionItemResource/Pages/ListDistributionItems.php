<?php

namespace App\Filament\Barangay\Resources\DistributionItemResource\Pages;

use App\Filament\Barangay\Resources\DistributionItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Redis\Connections\PhpRedisClusterConnection;

class ListDistributionItems extends ListRecords
{
    protected static string $resource = DistributionItemResource::class;




    public function mount(): void
    {
        $this->redirectRoute('filament.barangay.resources.distributions.index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
