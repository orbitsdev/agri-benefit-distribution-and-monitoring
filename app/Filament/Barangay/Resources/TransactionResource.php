<?php

namespace App\Filament\Barangay\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FilamentForm;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Barangay\Resources\TransactionResource\Pages;
use App\Filament\Barangay\Resources\TransactionResource\RelationManagers;
use App\Models\Distribution;

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

                Tables\Columns\TextColumn::make('action')->badge()
                ->color(fn (string $state): string => match ($state) {

                    'Claimed' => 'success',
                    'Unclaimed' => 'gray',
                })->label('Status')
                ,

                ColumnGroup::make('Beneficiary Information', [
                    Tables\Columns\TextColumn::make('beneficiary_details.name')
                    ->label('Beneficiary Name')
                    ->searchable()
                   ,

                    Tables\Columns\TextColumn::make('beneficiary_details.contact')->label('Contact')->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('beneficiary_details.email')->label('Email')->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('beneficiary_details.address')->label('Address')->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Item Details', [
                    // Tables\Columns\TextColumn::make('distributionItem.item.name')->searchable(),
                    // Tables\Columns\TextColumn::make('distribution_item_name')->searchable()->label('Item')->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Distribution Details', [
                    // Tables\Columns\TextColumn::make('distribution.title')->searchable()->wrap()->toggleable(isToggledHiddenByDefault: true),
                    // Tables\Columns\TextColumn::make('distribution_item_name')->searchable()->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Barangay Details ', [
                    // Tables\Columns\TextColumn::make('barangay.name')->searchable(),
                    // Tables\Columns\TextColumn::make('barangay_name')->searchable()->label('Barangay')->toggleable(isToggledHiddenByDefault: true),
                    // Tables\Columns\TextColumn::make('barangay_location')->searchable()->label('Barangay Location')->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Support Details', [
                    // Tables\Columns\TextColumn::make('support.personnel.user.name')->searchable(),
                    // Tables\Columns\TextColumn::make('support_name')->searchable()->label('Support Name')->toggleable(isToggledHiddenByDefault: true),
                    // Tables\Columns\TextColumn::make('support_type')->label('Support Type')->toggleable(isToggledHiddenByDefault: false),
                    // Tables\Columns\TextColumn::make('support_unique_code')->label('Support unique code')->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Record Details', [
                    // Tables\Columns\TextColumn::make('performed_at')
                    //     ->dateTime()
                    //     ->sortable()
                    //     ->toggleable(isToggledHiddenByDefault: true),
                    // Tables\Columns\TextColumn::make('created_at')
                    //     ->dateTime()
                    //     ->sortable()
                    //     ->toggleable(isToggledHiddenByDefault: true),
                    // Tables\Columns\TextColumn::make('updated_at')
                    //     ->dateTime()
                    //     ->sortable()
                    //     ->toggleable(isToggledHiddenByDefault: true),
                ]),



            ])
            ->filters([


                // SelectFilter::make('barangay_id')
                // ->relationship('barangay', 'name')
                // ->searchable()
                // ->multiple()
                // ->preload()->label('Barangay'),
                SelectFilter::make('distribution_id')
                ->relationship('distribution', 'title')
                ->searchable()
                // ->multiple()
                ->preload()->label('Distribution'),

                SelectFilter::make('distribution_item_id')
                ->searchable()
                ->getSearchResultsUsing(fn (string $search): array => Distribution::whereHas('item', function($query) use($search){
                    return $query->where('name', 'LIKE', "%{$search}%");

                })->limit(50)->pluck('name', 'id')->toArray())
                ->getOptionLabelUsing(fn ($value): ?string => Distribution::find($value)?->item->name)
                ->multiple()
                ->preload()->label('Item'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function($query){
                return $query->byBarangay(Auth::user()->barangay_id);
            })
            ;
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
