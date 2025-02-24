<?php

namespace App\Filament\Barangay\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use App\Models\Distribution;
use Filament\Resources\Resource;
use Filament\Actions\StaticAction;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FilamentForm;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Barangay\Resources\TransactionResource\Pages;
use App\Filament\Barangay\Resources\TransactionResource\RelationManagers;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'OPERATION MANAGEMENT';
    protected static bool $shouldRegisterNavigation = false;
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
                    ->label('Name')
                    ->searchable(isIndividual:true)
                   ,

                    Tables\Columns\TextColumn::make('beneficiary_details.contact')->searchable(isIndividual:true)->label('Contact')->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('beneficiary_details.email')->searchable(isIndividual:true)->label('Email')->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('beneficiary_details.address')->label('Address')->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Item Details', [
                    Tables\Columns\TextColumn::make('distribution_item_details.name')->searchable()->toggleable(isToggledHiddenByDefault: false)->label('Item')->wrap(),
                    // Tables\Columns\TextColumn::make('distribution_item_name')->searchable()->label('Item')->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Distribution Details', [
                    Tables\Columns\TextColumn::make('distribution_details.title')->searchable()->toggleable(isToggledHiddenByDefault: false)->label('Distribution'),
                    Tables\Columns\TextColumn::make('distribution_details.location')->toggleable(isToggledHiddenByDefault: true)->label('Location'),
                    Tables\Columns\TextColumn::make('distribution_details.date')
                    ->dateTime('M d, Y')
                    ->toggleable(isToggledHiddenByDefault: true)->label('Date'),
                    Tables\Columns\TextColumn::make('distribution_details.code')->searchable(isIndividual:true)->toggleable(isToggledHiddenByDefault: true)->label('Code'),
                    // Tables\Columns\TextColumn::make('distribution_item_name')->searchable()->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Barangay Details ', [
                    // Tables\Columns\TextColumn::make('barangay.name')->searchable(),
                    Tables\Columns\TextColumn::make('barangay_details.name')->searchable(isIndividual:true)->label('Barangay')->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('barangay_details.location')->searchable()->label('Location')->toggleable(isToggledHiddenByDefault: true),
                    // Tables\Columns\TextColumn::make('barangay_location')->searchable()->label('Barangay Location')->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Support Details', [
                    // Tables\Columns\TextColumn::make('support.personnel.user.name')->searchable(),
                    Tables\Columns\TextColumn::make('support_details.name')->searchable(isIndividual:true)->label('Name')->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('support_details.type')->label('Type')->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('support_details.unique_code')->searchable(isIndividual:true)->label('Support Code')->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Recorded Details', [
                    Tables\Columns\TextColumn::make('performed_at')
                    ->dateTime('M d, Y h:i A') // Format: Jan 21, 2025 10:30 AM
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

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
               

                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
