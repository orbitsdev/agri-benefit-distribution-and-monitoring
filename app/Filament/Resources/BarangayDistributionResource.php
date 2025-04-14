<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\BarangayDistribution;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Guava\FilamentNestedResources\Ancestor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\Filament\Resources\BarangayDistributionResource\Pages;
use App\Filament\Resources\BarangayDistributionResource\RelationManagers;

class BarangayDistributionResource extends Resource
{
    use NestedResource;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $model = BarangayDistribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Barangay Distribution';
    }

    //get ehading


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Distribution Details')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('barangay_id')
                                    ->label('Barangay')
                                    ->relationship('barangay', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\DatePicker::make('distribution_date')
                                    ->label('Distribution Date')
                                    ->required()
                                    ->native(false)
                                    ->closeOnDateSelection(),
                            ])->columns(2),

                        Forms\Components\TextInput::make('location')
                            ->label('Distribution Location')
                            ->required()
                            ->placeholder('Enter the distribution location')
                            ->maxLength(191),
                    ])->columns(1),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('distribution_id')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('barangay_id')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('distribution_date')
                //     ->date()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('location')
                //     ->searchable(),
                // Tables\Columns\IconColumn::make('is_disbursed')
                //     ->boolean(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BeneficiariesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangayDistributions::route('/'),
            'create' => Pages\CreateBarangayDistribution::route('/create'),
            'edit' => Pages\EditBarangayDistribution::route('/{record}/edit'),
        ];
    }
    public static function getAncestor(): ?Ancestor
    {
        return Ancestor::make(
            'barangayDistributions',
            'distribution',
        );
    }
}
