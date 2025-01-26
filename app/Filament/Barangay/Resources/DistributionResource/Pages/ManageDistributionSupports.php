<?php

namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Http\Controllers\FilamentForm;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Guava\FilamentNestedResources\Concerns\NestedPage;
use App\Filament\Barangay\Resources\DistributionResource;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;

class ManageDistributionSupports extends ManageRelatedRecords

{

    // use NestedPage;
    // use NestedRelationManager;
    protected static string $resource = DistributionResource::class;

    protected static string $relationship = 'supports';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected ?string $heading = 'Manage Supports';

    public static function getNavigationLabel(): string
    {
        return 'Distribution Supports';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(FilamentForm::supportForm());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('personnel.user.name')
            ->columns([
                Tables\Columns\TextColumn::make('personnel.user.name')->searchable(),
                // Tables\Columns\TextColumn::make('personnel.user.email')->searchable(),
                // Tables\Columns\TextColumn::make('personnel.contact_number'),
                Tables\Columns\TextColumn::make('unique_code')->searchable()->label('Code')->tooltip('This will be use for scanning QR code'),
                Tables\Columns\TextColumn::make('type'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->modalWidth('7xl'),
                // Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->color('gray'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
