<?php

namespace App\Filament\Resources\DistributionResource\Pages;

use Filament\Tables\Table;
use App\Jobs\SendQrMailJob;
use App\Models\Beneficiary;
use App\Models\Transaction;
use App\Models\Distribution;
use Filament\Infolists\Infolist;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Grouping\Group;

use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\DistributionResource;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Components\Section as InfolistSection;

class ListOfBarangayBeneficiaries extends Page  implements HasForms, HasTable, HasInfolists
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithInfolists;
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
                // First show the most important status columns
                TextColumn::make('cropsToReceive.is_claimed')
                    ->label('Claim Status')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'CLAIMED' : 'UNCLAIMED')
                    ->badge()
                    ->size('lg')
                    ->alignCenter()
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-badge' : 'heroicon-o-clock')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->searchable()
                    ->sortable(),

                ViewColumn::make('cropsToReceive.unique_code')->view('tables.columns.crop-qr')->label('QR Code'),

                TextColumn::make('cropsToReceive.date_claimed')
                    ->label('Date Claimed')
                    ->date('M d, Y h:i A')
                    ->placeholder('Not claimed yet')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('cropsToReceive.crop.name')
                    ->listWithLineBreaks()
                    ->badge()
                    ->color('primary')
                    ->label('Crop')
                    ->searchable(),

                // Then show beneficiary information
                TextColumn::make('rsbsa_no')
                    ->label('RSBSA')
                    ->searchable(),

                TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable(),

                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable(),

                TextColumn::make('is_approved')
                    ->label('Approved')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                    ->badge()
                    ->alignCenter()
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

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
            ])
            ->filters([
                SelectFilter::make('is_approved')
                    ->label('Approval Status')
                    ->options([
                        '1' => 'Approved',
                        '0' => 'Not Approved',
                    ])
                    ->placeholder('All Beneficiaries')
                    ->default(null),

                SelectFilter::make('is_claimed')
                    ->label('Claim Status')
                    ->options([
                        '1' => 'Claimed',
                        '0' => 'Not Claimed',
                    ])
                    ->placeholder('All Statuses')
                    ->default(null)
                    ->query(function ($query, array $data) {
                        return $query->when($data['value'] !== null, function ($query) use ($data) {
                            return $query->whereHas('cropsToReceive', function ($query) use ($data) {
                                return $query->where('is_claimed', $data['value']);
                            });
                        });
                    }),
            ])
            ->headerActions([
                Action::make('SendQr')
                ->label('Send QR to Emails')
                ->icon('heroicon-o-paper-airplane')
                ->button()
                ->outlined()
                ->hidden(function () {
                    // Only show if there are beneficiaries with emails
                    $count = Beneficiary::whereHas('barangayDistribution', function($query) {
                        $query->where('distribution_id', $this->record->id);
                    })->whereNotNull('email')->count();

                    return $count === 0;
                })
                ->requiresConfirmation()
                ->modalHeading('Confirm Sending QR Codes')
                ->modalSubheading('Are you sure you want to send QR codes to all beneficiaries with email addresses?')
                ->modalDescription(function () {
                    $count = Beneficiary::whereHas('barangayDistribution', function($query) {
                        $query->where('distribution_id', $this->record->id);
                    })->whereNotNull('email')->count();

                    return "This will send QR codes to {$count} beneficiaries with valid email addresses.";
                })
                ->action(function (): void {
                    // Get all beneficiaries with email addresses for this distribution
                    $beneficiaries = Beneficiary::whereHas('barangayDistribution', function($query) {
                        $query->where('distribution_id', $this->record->id);
                    })
                    ->whereNotNull('email')
                    ->whereHas('cropsToReceive', function($query) {
                        $query->whereNotNull('unique_code');
                    })
                    ->get();

                    $count = $beneficiaries->count();


                    if ($count > 0) {
                        foreach ($beneficiaries as $beneficiary) {
                            dispatch(new SendQrMailJob($beneficiary));
                        }

                        Notification::make()
                            ->title("Sending QR codes to {$count} beneficiaries")
                            ->body("Email delivery has been queued and will be processed in the background.")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('No emails to send')
                            ->body('No beneficiaries with valid email addresses and QR codes were found.')
                            ->warning()
                            ->send();
                    }
                })
                ->closeModalByClickingAway(false)
                ->modalWidth('md'),
                CreateAction::make()->modalWidth('6xl'),
            ])
            ->actions([
                Action::make('claim')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->label('Claim Benefit')
                    ->requiresConfirmation()
                    ->button()
                    ->modalHeading('Claim Benefit')
                    ->modalDescription('Are you sure you want to mark this benefit as claimed? This will record a transaction and update inventory.')
                    ->modalSubmitActionLabel('Yes, Claim Benefit')
                    ->action(function (Model $record) {
                        // Check if already claimed
                        if ($record->cropsToReceive->is_claimed) {
                            Notification::make()
                                ->title('Already Claimed')
                                ->body('This benefit has already been claimed.')
                                ->warning()
                                ->send();
                            return;
                        }

                        // Get the crop to update inventory
                        $crop = $record->cropsToReceive->crop;

                        // Start a database transaction to ensure all updates happen together
                        DB::beginTransaction();

                        try {
                            // Update crops to receive status
                            $record->cropsToReceive->is_claimed = true;
                            $record->cropsToReceive->date_claimed = now();
                            $record->cropsToReceive->save();

                            // Decrease the crop inventory using the helper method
                            $result = $crop->decreaseInventory();

                            if (!$result) {
                                throw new \Exception('Cannot decrease inventory. Stock limit reached or no stock available.');
                            }

                            // Record transaction
                            Transaction::recordClaim($record, 'Claimed');

                            // Commit the transaction
                            DB::commit();

                            Notification::make()
                                ->title('Benefit Claimed')
                                ->body('The benefit has been successfully claimed and inventory updated.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            // Roll back the transaction if anything goes wrong
                            DB::rollBack();

                            Notification::make()
                                ->title('Error')
                                ->body('Failed to claim benefit: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->hidden(fn (Model $record) =>
                        $record->cropsToReceive->is_claimed ||
                        !$record->barangayDistribution->is_disbursed
                    ),

                Action::make('revert_claim')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->label('Revert Claim')
                    ->button()
                    ->requiresConfirmation()
                    ->modalHeading('Revert Claim')
                    ->modalDescription('Are you sure you want to revert this claim? This will record a transaction and update inventory.')
                    ->modalSubmitActionLabel('Yes, Revert Claim')
                    ->action(function (Model $record) {
                        // Check if not claimed
                        if (!$record->cropsToReceive->is_claimed) {
                            Notification::make()
                                ->title('Not Claimed')
                                ->body('This benefit has not been claimed yet.')
                                ->warning()
                                ->send();
                            return;
                        }

                        // Get the crop to update inventory
                        $crop = $record->cropsToReceive->crop;

                        // Start a database transaction to ensure all updates happen together
                        DB::beginTransaction();

                        try {
                            // Update crops to receive status
                            $record->cropsToReceive->is_claimed = false;
                            $record->cropsToReceive->date_claimed = null;
                            $record->cropsToReceive->save();

                            // Increase the crop inventory using the helper method
                            $result = $crop->increaseInventory();

                            if (!$result) {
                                throw new \Exception('Cannot increase inventory. Original stock limit reached.');
                            }

                            // Record transaction
                            Transaction::recordClaim($record, 'Unclaimed');

                            // Commit the transaction
                            DB::commit();

                            Notification::make()
                                ->title('Claim Reverted')
                                ->body('The benefit claim has been successfully reverted and inventory updated.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            // Roll back the transaction if anything goes wrong
                            DB::rollBack();

                            Notification::make()
                                ->title('Error')
                                ->body('Failed to revert claim: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->hidden(fn (Model $record) =>
                        !$record->cropsToReceive->is_claimed ||
                        !$record->barangayDistribution->is_disbursed
                    ),

                ActionGroup::make([
                    Action::make('manageApproval')
                    ->label(fn (Model $record): string => $record->is_approved ? 'Disapprove' : 'Approve')
                    ->icon(fn (Model $record): string => $record->is_approved ? 'heroicon-o-x-circle' : 'heroicon-o-check-badge')
                    ->color(fn (Model $record): string => $record->is_approved ? 'danger' : 'success')
                    // ->button()
                    ->requiresConfirmation()
                    ->modalHeading(fn (Model $record): string => $record->is_approved ? 'Disapprove Beneficiary' : 'Approve Beneficiary')
                    ->modalDescription(fn (Model $record): string => $record->is_approved
                        ? 'Are you sure you want to disapprove this beneficiary? They will not be able to claim benefits until approved again.'
                        : 'Are you sure you want to approve this beneficiary? This will allow them to claim benefits when distribution is disbursed.')
                    ->modalSubmitActionLabel(fn (Model $record): string => $record->is_approved ? 'Yes, Disapprove' : 'Yes, Approve')
                    ->action(function (Model $record) {
                        $record->is_approved = !$record->is_approved;
                        $record->save();

                        $status = $record->is_approved ? 'approved' : 'disapproved';
                        Notification::make()
                            ->title("Beneficiary {$status}")
                            ->success()
                            ->send();
                    }),
                    Action::make('send_qr')
                    ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->requiresConfirmation()
                ->label('Send QR To Email ')
                ->action(function (Model $record) {
                    if (empty($record->email)) {
                        Notification::make()
                            ->title('Error')
                            ->body('The beneficiary does not have an email address.')
                            ->danger()
                            ->send();

                        return;
                    }

                    dispatch(new SendQrMailJob($record));

                    Notification::make()
                        ->title('Success')
                        ->body('QR Code has been sent successfully to ' . $record->email)
                        ->success()
                        ->send();
                }),
                    Action::make('View Qr')
                    ->color('gray')
                    ->label(' QR Code')
                    ->icon('heroicon-s-eye')

                    ->modalSubmitAction(false)
                    ->modalContent(fn(Model $record): View => view(
                        'livewire.crop-to-receive-qr',
                        ['code' => $record->cropsToReceive->unique_code],
                    ))
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                    ->closeModalByClickingAway(false)->modalWidth('7xl'),
                   ViewAction::make()->color('gray')->modalWidth('6xl')->infolist(fn (Infolist $infolist): Infolist => $this->infolist($infolist)),
                   EditAction::make()->modalWidth('6xl')->form([
                    Section::make('Personal Information')
                        ->schema([
                            TextInput::make('rsbsa_no')
                                ->label('RSBSA Number')
                                ->maxLength(255),

                            Grid::make()
                                ->schema([
                                    TextInput::make('first_name')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('middle_name')
                                        ->maxLength(255),

                                    TextInput::make('last_name')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('ext_name')
                                        ->label('Name Extension')
                                        ->maxLength(255),
                                ])->columns(2),

                            DatePicker::make('birthday')
                            ->date()
                                ->label('Date of Birth'),

                            Select::make('gender')
                                ->options([
                                    'male' => 'Male',
                                    'female' => 'Female',

                                ]),
                        ])->columns(1),

                    Section::make('Contact Information')
                        ->schema([
                            TextInput::make('contact_num')
                                ->label('Contact Number')
                                ->required()
                                ->maxLength(15)
                                ->tel()
                        ->prefix('+63')
                        ->mask('9999999999')
                                ->maxLength(255),

                            TextInput::make('email')
                                ->email()
                                ->maxLength(255),
                        ])->columns(2),

                    Section::make('Address Information')
                        ->schema([
                            TextInput::make('farmer_address')
                                ->label('Address')
                                ->maxLength(255),

                            TextInput::make('farmer_address_mun')
                                ->label('Municipality')
                                ->maxLength(255),

                            TextInput::make('farmer_address_prv')
                                ->label('Province')
                                ->maxLength(255),
                        ])->columns(1),

                    Section::make('Other Information')
                        ->schema([
                            TextInput::make('agency')
                                ->label('Agency')
                                ->maxLength(255),

                            Toggle::make('is_approved')
                                ->label('Approved')
                                ->default(true),
                        ])->columns(2),


                ])->label('Beneficiary Information'),

                   DeleteAction::make()->color('gray'),


                ])->label('Actions'),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->whereHas('barangayDistribution', function($query){
                    $query->where('distribution_id', $this->record->id);
                })->latest();
            })
            ->groups([
                Group::make('barangayDistribution.barangay.name')
                    ->label('Barangay')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
            ])
            ->defaultGroup('barangayDistribution.barangay.name')
            ->bulkActions([
                //BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Personal Information')
                    ->schema([
                        InfolistGrid::make(3)
                            ->schema([
                                TextEntry::make('rsbsa_no')
                                    ->label('RSBSA Number'),

                                TextEntry::make('first_name')
                                    ->label('First Name'),

                                TextEntry::make('middle_name')
                                    ->label('Middle Name'),

                                TextEntry::make('last_name')
                                    ->label('Last Name'),

                                TextEntry::make('ext_name')
                                    ->label('Name Extension'),

                                TextEntry::make('gender')
                                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                                TextEntry::make('birthday')
                                    ->label('Date of Birth')
                                    ->date(),
                            ]),
                    ]),

                InfolistSection::make('Contact Information')
                    ->schema([
                        InfolistGrid::make(2)
                            ->schema([
                                TextEntry::make('contact_num')
                                    ->label('Contact Number')
                                    ->formatStateUsing(fn ($state) => $state ? '+63 ' . substr($state, -10) : '-'),

                                TextEntry::make('email')
                                    ->label('Email Address'),
                            ]),
                    ]),

                InfolistSection::make('Address Information')
                    ->schema([
                        InfolistGrid::make(3)
                            ->schema([
                                TextEntry::make('farmer_address')
                                    ->label('Address'),

                                TextEntry::make('farmer_address_mun')
                                    ->label('Municipality'),

                                TextEntry::make('farmer_address_prv')
                                    ->label('Province'),
                            ]),
                    ]),

                InfolistSection::make('Other Information')
                    ->schema([
                        InfolistGrid::make(2)
                            ->schema([
                                TextEntry::make('agency')
                                    ->label('Agency'),

                                TextEntry::make('is_approved')
                                    ->label('Approval Status')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Approved' : 'Pending')
                                    ->badge()
                                    ->color(fn (string $state): string => $state === 'Approved' ? 'success' : 'gray'),
                            ]),
                    ]),

                InfolistSection::make('Crops to Receive')
                    ->schema([
                        TextEntry::make('cropsToReceive.crop.name')
                            ->label('Assigned Crop'),

                        TextEntry::make('cropsToReceive.is_claimed')
                            ->label('Claim Status')
                            ->formatStateUsing(fn ($state) => $state ? 'Claimed' : 'Not Claimed')
                            ->badge()
                            ->color(fn (string $state): string => $state === 'Claimed' ? 'success' : 'gray'),

                        TextEntry::make('cropsToReceive.date_claimed')
                            ->label('Date Claimed')
                            ->dateTime()
                            ->visible(fn ($record) => $record->cropsToReceive && $record->cropsToReceive->is_claimed),

                        TextEntry::make('cropsToReceive.unique_code')
                            ->label('QR Code')
                            ->formatStateUsing(fn ($state) => $state ?? 'No QR code assigned'),
                    ]),
            ]);
    }
}
