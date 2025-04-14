<?php

namespace App\Filament\Barangay\Resources\BarangayDistributionResource\Pages;

use Filament\Tables\Table;
use App\Jobs\SendQrMailJob;
use App\Models\Beneficiary;
use App\Models\Transaction;
use App\Models\CropToReceive;
use Filament\Infolists\Infolist;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Tables\Grouping\Group;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Barangay\Resources\BarangayDistributionResource;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Contracts\HasInfolists;

class ListOfBarangayBeneficiaries extends Page implements HasTable,HasInfolists
{
    use InteractsWithTable;
    use InteractsWithInfolists;

    protected static string $resource = BarangayDistributionResource::class;

    protected static string $view = 'filament.barangay.resources.barangay-distribution-resource.pages.list-of-barangay-beneficiaries';

    public $record;

    public function mount($record): void
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Beneficiary::query()
                    ->where('barangay_distribution_id', $this->record)
                    ->where('is_approved', true)
            )
            ->columns([

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
                ->wrap()
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
                    $count = Beneficiary::where('barangay_distribution_id', $this->record)
                        ->whereNotNull('email')
                        ->count();

                    return $count === 0;
                })
                ->requiresConfirmation()
                ->modalHeading('Confirm Sending QR Codes')
                ->modalSubheading('Are you sure you want to send QR codes to all beneficiaries with email addresses?')
                ->modalDescription(function () {
                    $count = Beneficiary::where('barangay_distribution_id', $this->record)
                        ->whereNotNull('email')
                        ->count();

                    return "This will send QR codes to {$count} beneficiaries with valid email addresses.";
                })
                ->action(function (): void {
                    // Get all beneficiaries with email addresses for this barangay distribution
                    $beneficiaries = Beneficiary::where('barangay_distribution_id', $this->record)
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





                ])->label('Actions'),
            ])
            ->bulkActions([
                // \Filament\Tables\Actions\BulkAction::make('send_bulk_qr')
                //     ->label('Send QR Codes')
                //     ->icon('heroicon-o-paper-airplane')
                //     ->requiresConfirmation()
                //     ->modalHeading('Send QR Codes')
                //     ->modalDescription('Are you sure you want to send QR codes to all selected beneficiaries? Only beneficiaries with valid email addresses will receive QR codes.')
                //     ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                //         $sentCount = 0;
                //         $failedCount = 0;

                //         foreach ($records as $record) {
                //             if ($record->is_approved && !empty($record->email)) {
                //                 // In a real implementation, this would send an email with the QR code
                //                 // For example: SendQRCodeEmail::dispatch($record, $record->cropsToReceive);
                //                 $sentCount++;
                //             } else {
                //                 $failedCount++;
                //             }
                //         }

                //         Notification::make()
                //             ->title('QR Codes Sent')
                //             ->body("Successfully queued {$sentCount} QR codes for sending. {$failedCount} beneficiaries were skipped due to missing email or approval.")
                //             ->success()
                //             ->send();
                //     })
            ])
            ->modifyQueryUsing(function (Builder $query) {
                // Filter beneficiaries by the current barangay distribution
                return $query->where('barangay_distribution_id', $this->record);
            });


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
