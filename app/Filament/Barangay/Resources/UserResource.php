<?php

namespace App\Filament\Barangay\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FilamentForm;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Barangay\Resources\UserResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Filament\Barangay\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Management';
    protected static ?string $navigationLabel = 'System Account';


    public static function form(Form $form): Form
    {
        return $form
        ->schema(FilamentForm::barangayUserForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image') ->defaultImageUrl(url('/images/placeholder-image.jpg'))->label('Profile')
                ->toggleable(isToggledHiddenByDefault: true)
                ,
                   Tables\Columns\TextColumn::make('name')
                       ->searchable(isIndividual:true),
                   Tables\Columns\TextColumn::make('barangay.name')
                       ->searchable(),
                   Tables\Columns\TextColumn::make('email')
                       ->searchable()
                       ->toggleable(isToggledHiddenByDefault: true)
                       ,
   
                       
   
                       Tables\Columns\TextColumn::make('role')
                       ->badge()
                       ->color(fn(string $state): string => match ($state) {
                           User::SUPER_ADMIN => 'success',
                           User::ADMIN => 'primary',
                           User::MEMBER=> 'primary',
   
                           default => 'gray'
                       }),
   
                       ToggleColumn::make('is_active')->label('Active/Disabled')->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('barangay_id')
                ->relationship('barangay', 'name')
                ->searchable()
                ->multiple()
                ->preload()->label('Barangay'),
                SelectFilter::make('role')
                ->options(User::ROLE_OPTIONS)->searchable()->multiple(),
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
            ->modifyQueryUsing(fn (Builder $query) => $query->isNotSuperAdmin()->byBarangay(Auth::user()->barangay_id)->IsNotAdmin())

            ->groups([
                Group::make('barangay.name')
                    ->label('Barangay')
                    ->titlePrefixedWithLabel(false)
                    ,
            ])
            ->defaultGroup('barangay.name')
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
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
