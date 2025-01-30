<?php

namespace App\Filament\Barangay\Pages;

use Filament\Pages\Page;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class ListOfBeneficiaries extends Page
{

    use InteractsWithRecord;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.barangay.pages.list-of-beneficiaries';

  
}
