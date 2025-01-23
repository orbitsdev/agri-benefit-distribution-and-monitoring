<?php

namespace App\Filament\Barangay\Resources\DistributionItemResource\Pages;

use App\Filament\Barangay\Resources\DistributionItemResource;
use App\Filament\Resources\AlbumResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;

class CreateDistributionBeneficiary extends CreateRelatedRecord
{
    use NestedPage;

    protected static string $resource = DistributionItemResource::class;
    protected static string $relationship = 'beneficiaries';
}