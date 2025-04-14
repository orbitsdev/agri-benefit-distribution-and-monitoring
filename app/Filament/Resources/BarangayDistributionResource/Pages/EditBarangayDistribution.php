<?php

namespace App\Filament\Resources\BarangayDistributionResource\Pages;

use App\Filament\Resources\BarangayDistributionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBarangayDistribution extends EditRecord
{
    protected static string $resource = BarangayDistributionResource::class;


    protected function getRedirectUrl(): string
    {

        // dd(DistributionResource::getResource()->getUrl('distributionItems'));
        // return ;
        // return $this->getResource()::getUrl('index');
        // dd(route('filament.barangay.resources.distributions.distributionItems',['record'=> $this->getRecord()->distribution_id]));
        return route('filament.admin.resources.distributions.barangayDistributions',['record'=> $this->getRecord()->distribution_id]);
        // return redirect()->route('filament.barangay.resources.distributions.distributionItems',['record'=> $this->getRecord()->distribution_id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
