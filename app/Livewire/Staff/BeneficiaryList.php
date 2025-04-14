<?php

namespace App\Livewire\Staff;

use Filament\Forms;
use Filament\Tables;
use App\Models\Beneficiary;
use Livewire\Component;
use Filament\Tables\Table;
use App\Models\Transaction;
use App\Jobs\SendQrMailJob;
use WireUi\Traits\WireUiActions;
use Filament\Actions\StaticAction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class BeneficiaryList extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use WireUiActions;

    public $distribution;

    public function mount($distribution = null)
    {
        $this->distribution = $distribution;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Beneficiary::query()
                    ->when($this->distribution, function ($query) {
                        return $query->where('barangay_distribution_id', $this->distribution);
                    })
                    ->where('is_approved', true)
                    ->whereHas('barangayDistribution', function ($query) {
                        $query->where('barangay_id', Auth::user()->barangay_id);
                    })
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
            ->actions([
                Tables\Actions\Action::make('claim')
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
                            $this->notification()->warning('Already Claimed', 'This benefit has already been claimed.');
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

                            // Dispatch event to refresh progress component
                            $this->dispatch('beneficiary-claimed', distribution: $record->barangay_distribution_id);

                            $this->notification()->success('Benefit Claimed', 'The benefit has been successfully claimed and inventory updated.');
                        } catch (\Exception $e) {
                            // Roll back the transaction if anything goes wrong
                            DB::rollBack();

                            $this->notification()->error('Error', 'Failed to claim benefit: ' . $e->getMessage());
                        }
                    })
                    ->hidden(fn (Model $record) =>
                        $record->cropsToReceive->is_claimed ||
                        !$record->barangayDistribution->is_disbursed
                    ),

                Tables\Actions\Action::make('revert_claim')
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
                            $this->notification()->warning('Not Claimed', 'This benefit has not been claimed yet.');
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

                            // Dispatch event to refresh progress component
                            $this->dispatch('beneficiary-unclaimed', distribution: $record->barangay_distribution_id);

                            $this->notification()->success('Claim Reverted', 'The benefit claim has been successfully reverted and inventory updated.');
                        } catch (\Exception $e) {
                            // Roll back the transaction if anything goes wrong
                            DB::rollBack();

                            $this->notification()->error('Error', 'Failed to revert claim: ' . $e->getMessage());
                        }
                    })
                    ->hidden(fn (Model $record) =>
                        !$record->cropsToReceive->is_claimed ||
                        !$record->barangayDistribution->is_disbursed
                    ),

                ActionGroup::make([
                    Tables\Actions\Action::make('send_qr')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->label('Send QR To Email')
                        ->action(function (Model $record) {
                            if (empty($record->email)) {
                                $this->notification()->error('Error', 'The beneficiary does not have an email address.');
                                return;
                            }

                            dispatch(new SendQrMailJob($record));

                            $this->notification()->success('Success', 'QR Code has been sent successfully to ' . $record->email);
                        }),

                    Tables\Actions\Action::make('view_qr')
                        ->color('gray')
                        ->label('QR Code')
                        ->icon('heroicon-s-eye')
                        ->modalSubmitAction(false)
                        ->modalContent(fn(Model $record): View => view(
                            'livewire.crop-to-receive-qr',
                            ['code' => $record->cropsToReceive->unique_code],
                        ))
                        ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                        ->closeModalByClickingAway(false)
                        ->modalWidth('7xl'),
                ])->label('Actions'),
            ])
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateHeading('No Beneficiaries Found')
            ->emptyStateDescription('There are no beneficiaries for this distribution yet.')
            ->striped();
    }

    public function render()
    {
        return view('livewire.staff.beneficiary-list');
    }
}
