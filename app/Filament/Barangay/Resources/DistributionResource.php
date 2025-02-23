<?php

namespace App\Filament\Barangay\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Distribution;
use Filament\Resources\Resource;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Select;
use App\Http\Controllers\FilamentForm;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Guava\FilamentNestedResources\Ancestor;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Filament\Barangay\Pages\ListOfBeneficiaries;
use App\Http\Middleware\EnsureDistributionIsUnlocked;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\Filament\Barangay\Resources\DistributionResource\Pages;
use App\Filament\Barangay\Resources\DistributionResource\RelationManagers;
use App\Filament\Barangay\Resources\DistributionItemResource\Pages\ListDistributionItems;
use App\Filament\Barangay\Resources\DistributionItemResource\Pages\CreateDistributionItem;
use App\Filament\Barangay\Resources\DistributionResource\Pages\ListOfDistributionBeneficiaries;

class DistributionResource extends Resource
{

    use NestedResource;

    protected static ?string $model = Distribution::class;

    protected static ?string $navigationIcon = 'solar-calendar-date-bold-duotone';
     protected static ?string $navigationGroup = 'OPERATION MANAGEMENT';

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
                ViewColumn::make('Items')->view('tables.columns.distribution-item-list')->label('Items|Quantity|Beneficiaries')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    Distribution::STATUS_PLANNED => 'gray',
                    Distribution::STATUS_ONGOING => 'success',
                    Distribution::STATUS_COMPLETED=> 'success',
                    Distribution::STATUS_CANCELED=> 'danger',

                    default => 'gray'
                }),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()->wrap(),





                Tables\Columns\TextColumn::make('location')
                    ->searchable()->label('Location/Venue')->wrap()->toggleable(isToggledHiddenByDefault: true),




                    Tables\Columns\TextColumn::make('code')
                    ->copyable()
                    ->searchable(),
                    Tables\Columns\TextColumn::make('distribution_date')
                    ->date()
                    ->label('Date')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                    Tables\Columns\TextColumn::make('is_locked')
                    ->label('Lock Status')
                    ->formatStateUsing(function ($state) {
                        return $state ? 'Locked' : 'Unlocked';
                    })
                    ->icon(function ($state) {
                        return $state ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open';
                    })
                                    ->color(fn(string $state): string => match ($state) {
                                        0 => 'gray',
                                        1=> 'success',
                                        default=> 'gray'

                                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                ->options(Distribution::STATUS_OPTIONS)->searchable()
            ])
            ->actions([
                Action::make('Lock and Unlock')->action(function(Model $record){

                    $record->is_locked = !$record->is_locked;
                    $record->save();


                    Notification::make()
                        ->title('Lock/Unlock Status')
                        ->success()
                        ->body("Status of distribution '{$record->title}' has been updated to " . ($record->is_locked ? 'Locked' : 'Unlocked') . ".")
                        ->send();

                })->requiresConfirmation()
                  ->button()

                  ->size(ActionSize::ExtraSmall)
                  ->outlined(function(Model $record){
                    return !$record->is_locked;
                  })
                  ->icon('heroicon-o-lock-closed')
                  ->color(function(Model $record){
                    return $record->is_locked ? 'danger' : 'primary';
                  })
                  ->label(function(Model $record){
                      return $record->is_locked ? 'Unlock' : 'Lock ';
                  })
                  ->modalDescription(function (Model $record) {
                    return $record->is_locked
                        ? "Are you sure you want to unlock this item? Unlocking will allow modifications. Be careful with your decision."
                        : "Are you sure you want to lock this item? Locking will prevent further modifications. Be careful with your decision.";
                })
                  ->tooltip(function(Model $record){
                    return $record->is_locked
                    ? 'This item is currently locked and cannot be modified. Be careful with your decision. Click to unlock and enable editing.'
                    : 'This item is currently unlocked. Be careful with your decision. Click to lock and prevent modifications.';
                  }),




                ActionGroup::make([
                    Action::make('update_status')
                    ->label('Change Status')
                    // ->button()
                    ->icon('heroicon-o-pencil-square')
                    ->size(ActionSize::ExtraSmall)
                    ->outlined()
      ->form([
          Select::make('status')
              ->label('Status')
              ->options(Distribution::STATUS_OPTIONS)
              ->required(),
      ])
      ->action(function (array $data, Model $record): void {
          $record->update($data);
      })->hidden(function(Model $record){
          return !$record->is_locked;
      }),
                    Action::make('View')
                    ->size(ActionSize::ExtraSmall)

                    ->label('View')
                    ->icon('heroicon-s-eye')
                    ->modalSubmitAction(false)
                    // ->button()

                    ->modalContent(fn(Model $record): View => view(
                        'livewire.view-distribution',
                        ['record' => $record],
                    ))
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                    ->closeModalByClickingAway(false)->modalWidth('7xl'),
                    Action::make('Beneficiaries') // Disable closing the modal by clicking outside
                    ->modalWidth('7xl')

                    ->label('Beneficiaries') // Add label for better UX
                    ->icon('heroicon-s-eye') // Optional: Add an icon for better UI
                    ->url(function (Model $record) {

                      return DistributionResource::getUrl('distribution-beneficiaries',['record'=>$record->id]);

                    }, shouldOpenInNewTab: true)
                  ,

                    Action::make('Transaction') // Disable closing the modal by clicking outside

                    ->size(ActionSize::ExtraSmall) // Set modal width
                    // ->button()
                    // ->outlined()

                    ->label('Transaction History') // Add label for better UX
                    ->icon('heroicon-s-clock')
                    ->url(function (Model $record) {

                      return DistributionResource::getUrl('distribution-transaction-history',['record'=>$record->id]);

                    }, shouldOpenInNewTab: true)
                  ,

                  Action::make('Supports')
    ->size(ActionSize::ExtraSmall)
    ->label('Support Lists')
    ->icon('heroicon-s-user-group')
    ->url(function (Model $record) {
        return route('export.supports', ['record' => $record->id]);
    }, shouldOpenInNewTab: true)
    ->hidden(function (Model $record) {
        return !$record->supports()->exists();
    })
,


                    Tables\Actions\EditAction::make()->label('Manage'),
                    Tables\Actions\DeleteAction::make()->color('gray'),




                ]),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'distribution-beneficiaries' => Pages\ListOfDistributionBeneficiaries::route('/{record}/distribution-beneficiaries'),
            'distribution-transaction-history' => Pages\TransactionHistory::route('/{record}/transaction-history'),
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
            Pages\ManageDistributionSupports::class,
            Pages\ManageDistributionDistributionItem::class,
            // Pages\ManageBe::class,
        ]);
    }

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }
}
