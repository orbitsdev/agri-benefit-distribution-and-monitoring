<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('User Details')
                ->description('Enter all required user information.')
                ->columns([
                    'sm' => 2,
                    'md' => 4,
                    'lg' => 6,
                    'xl' => 8,
                    '2xl' => 12,
                ])
                ->columnSpanFull()
                ->schema([
                   TextInput::make('name')
                    ->required()
                    ->maxLength(191)
                    ->columnSpan([
                        'sm' => 2,
                        'md' => 4,
                        'lg' => 4,
                    ]),

                    TextInput::make('email')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpan([
                        'sm' => 2,
                        'md' => 4,
                        'lg' => 4,
                    ]),

                    Select::make('role')
                    ->default(User::MEMBER)
                    ->required()
                    ->options(User::ROLE_OPTIONS)
                    ->columnSpan([
                        'sm' => 2,
                        'md' => 4,
                        'lg' => 4,
                    ])
                    ->searchable()
                    ->live()




                    ->disabled(fn(string $operation): bool => $operation === 'edit'),

                    TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->columnSpan([
                        'sm' => 2,
                        'md' => 4,
                        'lg' => 4,
                    ])
                    ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                    ->dehydrated(fn(?string $state): bool => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->label(fn(string $operation) => $operation == 'create' ? 'Password' : 'New Password'),
                ]),




            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
             SpatieMediaLibraryImageColumn::make('image') ->defaultImageUrl(url('/images/placeholder-image.jpg'))->label('Profile'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                    Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        User::SUPER_ADMIN => 'success',
                        User::ADMIN => 'primary',
                        User::MEMBER=> 'primary',

                        default => 'gray'
                    }),

            ])
            ->filters([
                SelectFilter::make('role')
                ->options(User::ROLE_OPTIONS)->searchable()->multiple()
            ])
            ->actions([
                    Tables\Actions\ViewAction::make()->button()->color('primary'),
                ActionGroup::make([

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->color('gray'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->isNotSuperAdmin())
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            // 'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
