<?php

namespace App\Filament\Barangay\Resources\DistributionItemResource\Pages;

use App\Filament\Barangay\Resources\DistributionItemResource;
use App\Filament\Barangay\Resources\DistributionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDistributionItem extends EditRecord
{
    protected static string $resource = DistributionItemResource::class;

    

    protected function getRedirectUrl(): string
    {

        // dd(DistributionResource::getResource()->getUrl('distributionItems'));
        // return ;
        // return $this->getResource()::getUrl('index');
        // dd(route('filament.barangay.resources.distributions.distributionItems',['record'=> $this->getRecord()->distribution_id]));
        return route('filament.barangay.resources.distributions.distributionItems',['record'=> $this->getRecord()->distribution_id]);
        // return redirect()->route('filament.barangay.resources.distributions.distributionItems',['record'=> $this->getRecord()->distribution_id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
