<?php

namespace App\Filament\Barangay\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SupportRole;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Barangay\Resources\SupportRoleResource\Pages;
use App\Filament\Barangay\Resources\SupportRoleResource\RelationManagers;

class SupportRoleResource extends Resource
{
    protected static ?string $model = SupportRole::class;

    protected static ?string $navigationIcon = 'vaadin-handshake';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'SETUP';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('description')
                    ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),

                    Tables\Columns\TextColumn::make('barangay.name')
                    ->searchable()->toggleable(),

                    ToggleColumn::make('is_active')->label('Active/Disabled')->alignCenter()->afterStateUpdated(function ($record, $state) {

                        if ($state) {
                            Notification::make()
                                ->title('Status was activated')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Status was deactivated')
                                ->success()
                                ->send()
                            ;
                        }
                    }),

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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ManageSupportRoles::route('/'),
        ];
    }
}
