<?php


namespace App\Filament\Resources\DistributionResource\Pages;


use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;

use Illuminate\Database\Eloquent\Model;

use Filament\Tables\Actions\ActionGroup;

use Illuminate\Database\Eloquent\Builder;

use App\Filament\Resources\DistributionResource;
use Filament\Resources\Pages\ManageRelatedRecords;

use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;

class ManageDistributionBarangay extends ManageRelatedRecords
{
    use NestedPage;
    use NestedRelationManager;

    protected static string $resource = DistributionResource::class;

    protected static string $relationship = 'barangayDistributions';

    protected static ?string $navigationIcon = 'hugeicons-city-01';

    protected static ?string $navigationLabel = 'Barangay';

    protected ?string $heading = 'Barangay';

     // public static function getNavigationLabel(): string
    // {
    //     return 'Distribution Items';
    // }

    public static function getNavigationLabel(): string
    {
        return 'Barangay';
    }

    // protected function getDistribution(): Model
    // {
    //     return $this->getOwnerRecord();
    // }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item_id')
            ->columns([
                TextColumn::make('barangay.name')->searchable()->label('Barangay'),

                TextColumn::make('is_disbursed')
                    ->label('Disbursement Status')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Disbursed' : 'Not Disbursed')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'gray')
                    ->icon(fn(bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),

                TextColumn::make('distribution_date')
                    ->date()
                    ->sortable()
                    ->label('Distribution Date'),

                TextColumn::make('location')
                    ->searchable()
                    ->wrap()
                    ->label('Distribution Venue'),

                TextColumn::make('beneficiaries_count')
                    ->label('Total Beneficiaries')
                    ->counts('beneficiaries')
                    ->alignCenter(),

                // TextColumn::make('claimed_beneficiaries_count')
                //     ->label('Claimed')
                //     ->counts('beneficiaries', function ($query) {
                //         return $query->whereHas('cropsToReceive', function ($query) {
                //             return $query->where('is_claimed', true);
                //         });
                //     })
                //     ->alignCenter(),

                // TextColumn::make('unclaimed_beneficiaries_count')
                //     ->label('Unclaimed')
                //     ->counts('beneficiaries', function ($query) {
                //         return $query->whereHas('cropsToReceive', function ($query) {
                //             return $query->where('is_claimed', false);
                //         });
                //     })
                //     ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->actionsPosition(Tables\Enums\ActionsPosition::BeforeColumns)
            ->actions([


                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                    ->label('Manage')

                    ->icon('heroicon-o-pencil-square')
                    ->color('gray')
                    ->form([
                        Forms\Components\DatePicker::make('distribution_date')
                            ->label('Distribution Date')
                            ->required(),
                        Forms\Components\TextInput::make('location')
                            ->label('Distribution Venue/Location')
                            ->required(),
                    ]),
                    Tables\Actions\Action::make('disburse')
                    ->label(fn (Model $record): string => $record->is_disbursed ? 'Undo Disbursement' : 'Disburse')
                    ->requiresConfirmation()
                    ->modalDescription(fn (Model $record): string => $record->is_disbursed
                        ? 'Are you sure you want to cancel the disbursement status? This will prevent beneficiaries from claiming benefits.'
                        : 'Are you sure you want to mark this barangay distribution as disbursed? This will allow beneficiaries to claim their benefits.')
                    ->icon('heroicon-o-banknotes')
                    ->color(fn (Model $record): string => $record->is_disbursed ? 'danger' : 'success')
                    //
                    ->action(function (Model $record): void {
                        $record->is_disbursed = !$record->is_disbursed;
                        $record->save();

                        $status = $record->is_disbursed ? 'disbursed' : 'undisbursed';
                        \Filament\Notifications\Notification::make()
                            ->title("Barangay distribution marked as {$status}")
                            ->success()
                            ->send();
                    }),

                    ]),

                // Tables\Actions\Action::make('beneficiaries')
                //     ->label('Manage Beneficiaries')
                //     ->icon('heroicon-o-user-group')
                //     ->color('gray')
                //     ->button()
                //     ->url(fn (Model $record) => DistributionResource::getUrl('distribution-beneficiaries', ['record' => $record->distribution_id, 'barangay_id' => $record->id])),
            ])
            ->bulkActions([

            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('distribution_id', $this->getRecord()->id);
            })->paginated([20, 35, 50, 100, 'all']);
        ;
    }
}
