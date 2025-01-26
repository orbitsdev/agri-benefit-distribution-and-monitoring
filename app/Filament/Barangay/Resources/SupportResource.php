<?php

namespace App\Filament\Barangay\Resources;

use App\Filament\Barangay\Resources\SupportResource\Pages;
use App\Filament\Barangay\Resources\SupportResource\RelationManagers;
use App\Models\Support;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupportResource extends Resource
{
    protected static ?string $model = Support::class;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('personnel_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('distribution_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('unique_code')
                    ->maxLength(191),
                Forms\Components\Toggle::make('can_scan_qr')
                    ->required(),
                Forms\Components\Toggle::make('can_register')
                    ->required(),
                Forms\Components\Toggle::make('can_confirm_claims')
                    ->required(),
                Forms\Components\Toggle::make('can_view_list')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('personnel_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('distribution_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unique_code')
                    ->searchable(),
                Tables\Columns\IconColumn::make('can_scan_qr')
                    ->boolean(),
                Tables\Columns\IconColumn::make('can_register')
                    ->boolean(),
                Tables\Columns\IconColumn::make('can_confirm_claims')
                    ->boolean(),
                Tables\Columns\IconColumn::make('can_view_list')
                    ->boolean(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupports::route('/'),
            'create' => Pages\CreateSupport::route('/create'),
            'edit' => Pages\EditSupport::route('/{record}/edit'),
        ];
    }
}
