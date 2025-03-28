<?php

namespace App\Filament\Barangay\Resources;

use Filament\Forms;
use App\Models\Item;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Barangay\Resources\ItemResource\Pages;
use App\Filament\Barangay\Resources\ItemResource\RelationManagers;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;
    protected static ?string $navigationLabel = 'Registered Items';
    protected static ?string $navigationIcon = 'heroicon-s-archive-box-arrow-down';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'SETUP';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('type')
                    ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->label('Item Name'),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('barangay.name')
                    ->searchable(),

                ToggleColumn::make('is_active')->label('Active')->alignCenter()->toggleable()
                    ->afterStateUpdated(function ($record, $state) {

                        if ($state) {
                            Notification::make()
                                ->title('Item was activated')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Item was deactivated')
                                ->success()
                                ->send()
                            ;
                        }
                    }),

                    Tables\Columns\TextColumn::make('distribution_items_count')->counts('distribution_items')->label('Usage'),
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
                Tables\Actions\DeleteAction::make()->color('gray')->visible(fn (Model $record) => !$record->has_distribution_items),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->striped()
            ->modifyQueryUsing(function (Builder $query) {
                $query->byBarangay(Auth::user()->barangay_id)->latest();
            })
            ;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageItems::route('/'),
        ];
    }
}
