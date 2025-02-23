<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Barangay;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Http\Controllers\FilamentForm;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BarangayResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Filament\Resources\BarangayResource\RelationManagers;

class BarangayResource extends Resource
{
    protected static ?string $model = Barangay::class;

    protected static ?string $navigationIcon = 'hugeicons-city-01';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(FilamentForm::barangayForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                ->defaultImageUrl(url('/images/placeholder-image.jpg'))
                ->label('Profile')
                ->toggleable(isToggledHiddenByDefault: false)
                ->getStateUsing(function (Model $record): string {
                    return  $record->getFirstMediaUrl('image');
                })
                ->extraImgAttributes([
                    'img' => 'src'
                ])
                ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('chairman_name')
                    ->searchable()->label('Chairman')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('chairman_contact')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('head_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('head_contact')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
              
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangays::route('/'),
            'create' => Pages\CreateBarangay::route('/create'),
            // 'view' => Pages\ViewBarangay::route('/{record}'),
            'edit' => Pages\EditBarangay::route('/{record}/edit'),
        ];
    }
}
