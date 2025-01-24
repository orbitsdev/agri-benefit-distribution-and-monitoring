<?php

namespace App\Filament\Barangay\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\DistributionItem;
use Filament\Resources\Resource;
use App\Http\Controllers\FilamentForm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Guava\FilamentNestedResources\Ancestor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\Filament\Barangay\Resources\DistributionItemResource\Pages;
use App\Filament\Barangay\Resources\DistributionItemResource\RelationManagers;

class DistributionItemResource extends Resource
{
    use NestedResource;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = DistributionItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

//    public static function getBreadcrumbRecordLabel(Model $record)
// {
//     return $record->first_name . ' ' . $record->last_name;
// }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(FilamentForm::distributeItems());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('item_id')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('distribution_id')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
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
            RelationManagers\BeneficiariesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistributionItems::route('/'),
            'create' => Pages\CreateDistributionItem::route('/create'),
            'edit' => Pages\EditDistributionItem::route('/{record}/edit'),
            'beneficiaries.create' => Pages\CreateDistributionBeneficiary::route('/{record}/songs/beneficiaries'),
        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        return Ancestor::make(
            'distributionItems',
            'distribution',
        );
    }

}
