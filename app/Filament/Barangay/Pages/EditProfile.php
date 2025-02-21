<?php

namespace App\Filament\Barangay\Pages;

use Filament\Pages\Page;
use App\Http\Controllers\FilamentForm;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
class EditProfile extends BaseEditProfile
{
    public function getHeading(): string
    {
        return 'My Profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(FilamentForm::profileForm());
    }
}
