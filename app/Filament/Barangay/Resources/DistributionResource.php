<?php

namespace App\Filament\Barangay\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Distribution;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use App\Http\Controllers\FilamentForm;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Guava\FilamentNestedResources\Ancestor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\Filament\Barangay\Resources\DistributionResource\Pages;
use App\Filament\Barangay\Resources\DistributionResource\RelationManagers;
use App\Filament\Barangay\Resources\DistributionItemResource\Pages\ListDistributionItems;
use App\Filament\Barangay\Resources\DistributionItemResource\Pages\CreateDistributionItem;

class DistributionResource extends Resource
{

    use NestedResource;

    protected static ?string $model = Distribution::class;

    protected static ?string $navigationIcon = 'solar-calendar-date-bold-duotone';
     protected static ?string $navigationGroup = 'Transactions';

     //sort
        protected static ?int $navigationSort = 3;

    public static function getBreadcrumb(): string
    {
        return 'Desritbution';
    }

    //disabled breadcrumb url
    public static function getBreadcrumbUrl(): string
    {
        return '/distribution';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(FilamentForm::distributionForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('barangay.name')->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()->wrap(),
                Tables\Columns\TextColumn::make('distribution_date')
                    ->date()
                    ->label('Date')
                    ->sortable(),
                    Tables\Columns\TextColumn::make('code')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()->label('Location/Venue')->wrap()->toggleable(isToggledHiddenByDefault: true),

                    ViewColumn::make('Items')->view('tables.columns.distribution-item-list')->label('Items|Quantity|Beneficiaries'),

                    Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        Distribution::STATUS_PLANNED => 'gray',
                        Distribution::STATUS_ONGOING => 'success',
                        Distribution::STATUS_COMPLETED=> 'success',
                        Distribution::STATUS_CANCELED=> 'danger',

                        default => 'gray'
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
                SelectFilter::make('status')
                ->options(Distribution::STATUS_OPTIONS)->searchable()
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->label('Manage'),
                    Tables\Actions\DeleteAction::make()->color('gray'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->byBarangay(auth()->user()->barangay_id);
            })
            ;
    }

    public static function getRelations(): array
    {
        return [
            //  RelationManagers\DistributionItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistributions::route('/'),
            'create' => Pages\CreateDistribution::route('/create'),
            // 'view' => Pages\ViewDistribution::route('/{record}'),
            'edit' => Pages\EditDistribution::route('/{record}/edit'),
            'distributionItems' => Pages\ManageDistributionDistributionItem::route('/{record}/distributionItems'),
            'supports' => Pages\ManageDistributionSupports::route('/{record}/supports'),

            'distributionItems.create' => Pages\CreateDistributionDistributionItem::route('/{record}/distribution/distributionItems'),
            'supports.create' => Pages\CreateDistributionSupport::route('/{record}/distribution/supports'),


        ];
    }
    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditDistribution::class,
            Pages\ManageDistributionDistributionItem::class,
            Pages\ManageDistributionSupports::class,
            // Pages\ManageBe::class,
        ]);
    }

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }
}
