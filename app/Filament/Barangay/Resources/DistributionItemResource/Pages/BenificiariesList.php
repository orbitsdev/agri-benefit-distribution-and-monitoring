<?php

namespace App\Filament\Barangay\Resources\DistributionItemResource\Pages;

use App\Filament\Barangay\Resources\DistributionItemResource;
use Filament\Resources\Pages\Page;

class BenificiariesList extends Page
{
    protected static string $resource = DistributionItemResource::class;

    protected static string $view = 'filament.barangay.resources.distribution-item-resource.pages.benificiaries-list';
}
