<?php

namespace App\Filament\Barangay\Resources;

use App\Filament\Barangay\Resources\FailedJobResource\Pages;
use App\Filament\Barangay\Resources\FailedJobResource\RelationManagers;
use App\Models\FailedJob;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'Diagnostics';
    protected static ?string $navigationLabel = 'Email Logs';

    protected static ?int $navigationSort = 11;
    protected static bool $shouldRegisterNavigation = false;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('uuid')
                //     ->label('UUID')
                //     ->required()
                //     ->maxLength(191),
                // Forms\Components\Textarea::make('connection')
                //     ->required()
                //     ->columnSpanFull(),
                // Forms\Components\Textarea::make('queue')
                //     ->required()
                //     ->columnSpanFull(),
                // Forms\Components\Textarea::make('payload')
                //     ->required()
                //     ->columnSpanFull(),
                // Forms\Components\Textarea::make('exception')
                //     ->required()
                //     ->columnSpanFull(),
                // Forms\Components\DateTimePicker::make('failed_at')
                //     ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('exception')
                    ->label('Message')
                    ->searchable(),
                Tables\Columns\TextColumn::make('failed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListFailedJobs::route('/'),
            'create' => Pages\CreateFailedJob::route('/create'),
            'view' => Pages\ViewFailedJob::route('/{record}'),
            'edit' => Pages\EditFailedJob::route('/{record}/edit'),
        ];
    }
}
