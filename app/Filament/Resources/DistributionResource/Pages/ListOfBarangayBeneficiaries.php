<?php

namespace App\Filament\Resources\DistributionResource\Pages;

use Filament\Tables\Table;
use App\Models\Beneficiary;
use App\Models\Distribution;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;

use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\DistributionResource;
use Filament\Tables\Concerns\InteractsWithTable;

class ListOfBarangayBeneficiaries extends Page  implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    protected static string $resource = DistributionResource::class;

    protected static string $view = 'filament.resources.distribution-resource.pages.list-of-barangay-beneficiaries';

    public $record;

    public function getHeading(): string
{
    return $this->record->title. ' Beneficiaries';
}

    public function mount(Distribution $record): void
{
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
        ->query(Beneficiary::query())
            ->columns([
                // Tables\Columns\TextColumn::make('unique_code')
                // ->label('Code')
                // ,
                ViewColumn::make('cropsToReceive.unique_code')->view('tables.columns.crop-qr')->label('Code'),
               TextColumn::make('is_approved')
                    ->label('Approved')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                    ->badge()
                    ->alignCenter()
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                // Always visible columns
               TextColumn::make('rsbsa_no')
                    ->label('RSBSA')
                    ->searchable(),

               TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable(),

               TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable(),

               TextColumn::make('email')
                    ->searchable(),

                // Toggleable columns
               TextColumn::make('contact_num')
                    ->label('Contact')
                    ->formatStateUsing(fn ($state) => $state ? '+63 ' . substr($state, -10) : '-')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

               TextColumn::make('gender')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

               TextColumn::make('birthday')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),

               TextColumn::make('farmer_address')
                    ->label('Address')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

               TextColumn::make('farmer_address_mun')
                    ->label('Municipality')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

               TextColumn::make('cropsToReceive.crop.name')
                    ->listWithLineBreaks()->badge()->color('primary')->label('Crop'),

                // Tables\Columns\TextColumn::make('farmer_address_prv')
                //     ->label('Province')
                //     ->searchable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                //     ToggleColumn::make('is_approved')->label('Status')->alignCenter()->afterStateUpdated(function ($record, $state) {

                //         if ($state) {
                //             Notification::make()
                //                 ->title('Beneficiary was approved')
                //                 ->success()
                //                 ->send();
                //         } else {
                //             Notification::make()
                //                 ->title('Beneficiary was disapproved')
                //                 ->success()
                //                 ->send()
                //             ;
                //         }
                //     }),

                // Tables\Columns\TextColumn::make('agency')
                //     ->searchable()
                //     ->toggleable(isToggledHiddenByDefault: true),



                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->modalWidth('6xl'),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('View Qr')
                    ->color('gray')
                    ->label('View QR Code')
                    ->icon('heroicon-s-eye')

                    ->modalSubmitAction(false)
                    ->modalContent(fn(Model $record): View => view(
                        'livewire.crop-to-receive-qr',
                        ['code' => $record->cropsToReceive->unique_code],
                    ))
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                    ->closeModalByClickingAway(false)->modalWidth('7xl'),
                   ViewAction::make()->color('gray')->modalWidth('6xl'),
                   EditAction::make()->modalWidth('6xl'),
                   DeleteAction::make()->color('gray'),
                   Action::make('manageApproval')
                        ->label('Manage Approval')
                        ->icon('heroicon-o-check-badge')
                        ->requiresConfirmation()
                        ->form([
                           Toggle::make('is_approved')
                                ->label('Approve Beneficiary')
                                ->default(function ($record) {
                                    return $record->is_approved;
                                })
                                ->helperText('Toggle to approve or disapprove this beneficiary.')
                        ])
                        ->action(function ($record, array $data) {
                            $record->is_approved = $data['is_approved'];
                            $record->save();

                            $status = $data['is_approved'] ? 'approved' : 'disapproved';
                            Notification::make()
                                ->title("Beneficiary {$status}")
                                ->success()
                                ->send();
                        })
                ])->label('Actions'),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->whereHas('barangayDistribution', function($query){
                    $query->where('distribution_id', $this->record->id);
                })->latest();
            })
            ->bulkActions([
                //BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
