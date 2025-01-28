<?php

namespace App\Filament\Barangay\Resources;

use App\Filament\Barangay\Resources\ImportFailureResource\Pages;
use App\Filament\Barangay\Resources\ImportFailureResource\RelationManagers;
use App\Models\ImportFailure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImportFailureResource extends Resource
{
    protected static ?string $model = ImportFailure::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';

    protected static ?string $navigationGroup = 'Diagnostics';
    protected static ?string $navigationLabel = 'Import Logs';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('distribution_id')
                //     ->required()
                //     ->numeric(),
                // Forms\Components\Textarea::make('row_data')
                //     ->required()
                //     ->columnSpanFull(),
                // Forms\Components\TextInput::make('error_message')
                //     ->required()
                //     ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('distribution.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('error_message')->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()->color('gray'),
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
            'index' => Pages\ListImportFailures::route('/'),
            'create' => Pages\CreateImportFailure::route('/create'),
            'view' => Pages\ViewImportFailure::route('/{record}'),
            'edit' => Pages\EditImportFailure::route('/{record}/edit'),
        ];
    }
}
