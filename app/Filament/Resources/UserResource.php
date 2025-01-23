<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use App\Http\Controllers\FilamentForm;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ToggleColumn;
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
            ->schema(FilamentForm::userForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
             SpatieMediaLibraryImageColumn::make('image') ->defaultImageUrl(url('/images/placeholder-image.jpg'))->label('Profile')
             ->toggleable(isToggledHiddenByDefault: true)
             ,
                Tables\Columns\TextColumn::make('name')
                    ->searchable(isIndividual :true),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ,

                    ToggleColumn::make('is_active')->label('Active/Disabled'),

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
