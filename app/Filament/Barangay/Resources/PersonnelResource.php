<?php

namespace App\Filament\Barangay\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Personnel;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Http\Controllers\FilamentForm;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Barangay\Resources\PersonnelResource\Pages;
use App\Filament\Barangay\Resources\PersonnelResource\RelationManagers;

class PersonnelResource extends Resource
{
    protected static ?string $model = Personnel::class;

    protected static ?string $navigationIcon = 'fluentui-person-24';
    protected static ?string $navigationGroup = 'Management';


    //sort 
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form->schema(FilamentForm::personnelForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()->label('Name'),
                Tables\Columns\TextColumn::make(name: 'user.email')
                    ->searchable()->label('Email'),
                Tables\Columns\TextColumn::make(name: 'contact_number')
                    ->searchable()->label('Contact Number'),
                Tables\Columns\TextColumn::make('position')
                ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_number')
                    ->searchable(),
                 
                ToggleColumn::make('is_active')->label('Active'),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonnels::route('/'),
            'create' => Pages\CreatePersonnel::route('/create'),
            'view' => Pages\ViewPersonnel::route('/{record}'),
            'edit' => Pages\EditPersonnel::route('/{record}/edit'),
        ];
    }
}
