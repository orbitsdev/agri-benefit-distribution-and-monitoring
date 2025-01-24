<?php

namespace App\Filament\Barangay\Resources;

use App\Filament\Barangay\Resources\DistributionItemResource\Pages\CreateDistributionItem;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Distribution;
use Filament\Resources\Resource;
use App\Http\Controllers\FilamentForm;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Guava\FilamentNestedResources\Ancestor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Barangay\Resources\DistributionResource\Pages;
use App\Filament\Barangay\Resources\DistributionResource\RelationManagers;
use App\Filament\Barangay\Resources\DistributionItemResource\Pages\ListDistributionItems;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Filament\Resources\Pages\Page;
class DistributionResource extends Resource
{

    use NestedResource;

    protected static ?string $model = Distribution::class;

    protected static ?string $navigationIcon = 'solar-calendar-date-bold-duotone';



    public static function form(Form $form): Form
    {
        return $form
            ->schema(FilamentForm::distributionForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('barangay.name')->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()->wrap(),
                Tables\Columns\TextColumn::make('distribution_date')
                    ->date()
                    ->label('Date')
                    ->sortable(),
                    Tables\Columns\TextColumn::make('code')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()->label('Location/Venue')->wrap()->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //  RelationManagers\DistributionItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistributions::route('/'),
            'create' => Pages\CreateDistribution::route('/create'),
            // 'view' => Pages\ViewDistribution::route('/{record}'),
            'edit' => Pages\EditDistribution::route('/{record}/edit'),
            'distributionItems' => Pages\ManageDistributionDistributionItem::route('/{record}/distributionItems'),

            'distributionItems.create' => Pages\CreateDistributionDistributionItem::route('/{record}/distribution/distributionItems'),


        ];
    }
    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditDistribution::class,
            Pages\ManageDistributionDistributionItem::class,
            // Pages\ManageBe::class,
        ]);
    }

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }
}
