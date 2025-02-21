<?php

namespace App\Filament\Barangay\Resources\SupportResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Barangay\Resources\SupportResource;

class CreateSupport extends CreateRecord
{
    protected static string $resource = SupportResource::class;
   
}
