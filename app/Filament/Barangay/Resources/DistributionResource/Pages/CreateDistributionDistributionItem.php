<?php
namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use App\Filament\Barangay\Resources\DistributionResource;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;

class CreateDistributionDistributionItem extends CreateRelatedRecord
{
    use NestedPage;

    // This page also needs to know the ancestor relationship used (just like relation managers):
    // protected static string $resource = DistributionResource::class;
    protected static string $resource = DistributionResource::class;
    protected static string $relationship = 'distributionItems';

    // We can usually guess the nested resource, but if your app has multiple resources for this
    // model, you will need to explicitly define it
    // public static string $nestedResource = AlbumResource::class;

    protected function getRedirectUrl(): string
    {

       
        return route('filament.barangay.resources.distributions.distributionItems',['record'=> $this->getRecord()->distribution_id]);

    }

    protected function mutateFormDataBeforeCreate(array $data): array
{
    // $data['user_id'] = auth()->id();

    // dd($this->getRecord());
    // dd($data);
    return $data;
}

protected function handleRecordCreation(array $data): Model
{
    //  dd($data);
    // return static::getModel()::create($data);
   return $this->getRecord()->distributionItems()->create($data);
}
}
