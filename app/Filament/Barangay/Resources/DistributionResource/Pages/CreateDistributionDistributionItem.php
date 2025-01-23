<?php
namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use App\Filament\Barangay\Resources\DistributionResource;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;
 
class CreateDistributionDistributionItem extends CreateRelatedRecord
{
    use NestedPage;
 
    // This page also needs to know the ancestor relationship used (just like relation managers):
    protected static string $resource = DistributionResource::class;
    protected static string $relationship = 'distributionItems';
 
    // We can usually guess the nested resource, but if your app has multiple resources for this
    // model, you will need to explicitly define it
    // public static string $nestedResource = AlbumResource::class;
}