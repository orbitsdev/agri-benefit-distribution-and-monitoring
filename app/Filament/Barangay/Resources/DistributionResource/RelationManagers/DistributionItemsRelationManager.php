<?php

namespace App\Filament\Barangay\Resources\DistributionResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class DistributionItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'distributionItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('item_id')
                                        ->label('Item')
                                        ->relationship(
                                            'item',
                                            'id',
                                            modifyQueryUsing: fn(Builder $query) => $query,
                                        )
                                        ->distinct()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name}")
                                        ->searchable(['name'])
                                        ->preload()
                                        ->required(),
                                    TextInput::make('quantity')
                                        ->numeric()
                                        
                                        ->minValue(1)
                                        ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item_id')
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->searchable()
                    ->label('Item'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
