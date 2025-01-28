<?php

namespace App\Filament\Barangay\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Resources\Resource;
use App\Http\Controllers\FilamentForm;
use Filament\Tables\Columns\ColumnGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Barangay\Resources\TransactionResource\Pages;
use App\Filament\Barangay\Resources\TransactionResource\RelationManagers;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Operations Management';
    // protected static ?string $navigationLabel = 'Import Logs';



    public static function form(Form $form): Form
    {
        return $form
            ->schema(FilamentForm::transactionForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('action'),

                ColumnGroup::make('Beneficiary', [
                    Tables\Columns\TextColumn::make('beneficiary.name')->searchable()->label('Beneficiary Name'),
                    // Tables\Columns\TextColumn::make('beneficiary_name')->searchable()->label('Name (Original)')->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('beneficiary_contact')->label('Contact (Original)')->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('beneficiary_email')->searchable()->label('Email (Original)')->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Item', [
                    Tables\Columns\TextColumn::make('distributionItem.item.name')->searchable(),
                    Tables\Columns\TextColumn::make('distribution_item_name')->searchable()->label('Item (Original)')->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Distribution', [
                    Tables\Columns\TextColumn::make('distribution.title')->searchable()->wrap()->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('distribution_item_name')->searchable()->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Barangay ', [
                    Tables\Columns\TextColumn::make('barangay.name')->searchable(),
                    Tables\Columns\TextColumn::make('barangay_name')->searchable()->label('Barangay (Original)')->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('barangay_location')->searchable()->label('Barangay Location (Original)')->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Support', [
                    Tables\Columns\TextColumn::make('support.personnel.user.name')->searchable(),
                    Tables\Columns\TextColumn::make('support_name')->searchable()->label('Support Name (Original)')->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('support_type')->label('Support Type (Original)')->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('support_unique_code')->label('Support unique code (Original)')->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Record', [
                    Tables\Columns\TextColumn::make('performed_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('created_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('updated_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),



            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
