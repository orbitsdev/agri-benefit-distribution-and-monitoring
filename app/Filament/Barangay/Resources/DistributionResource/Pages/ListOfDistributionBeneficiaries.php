<?php

namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use App\Models\User;
use Filament\Tables\Table;
use App\Jobs\SendQrMailJob;
use App\Models\Beneficiary;
use App\Models\Transaction;
use App\Models\Distribution;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Contracts\HasTable;

use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Barangay\Resources\DistributionResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class ListOfDistributionBeneficiaries extends Page  implements HasForms, HasTable

{

    use InteractsWithTable;
    use InteractsWithForms;
    protected static string $resource = DistributionResource::class;

    protected static string $view = 'filament.barangay.resources.distribution-resource.pages.list-of-distribution-beneficiaries';

    public $record;

    public function mount(Distribution $record): void
{
        $this->record = $record;
    }

    public function getHeading(): string
    {
        $item = $this->record->title;
        return __($item . ' Benificiaries ');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Beneficiary::query())
            ->columns([
                TextColumn::make('status')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    Beneficiary::CLAIMED => 'success',
                    default => 'gray'
                }),
                ViewColumn::make('code')->view('tables.columns.beneficiary-qr'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('contact')->searchable(),
                TextColumn::make('address')->searchable()->wrap(),
                TextColumn::make('distributionItem.item.name')->searchable(),
                // TextColumn::make('status')
                //     ->badge()
                //     ->color(fn(string $state): string => match ($state) {
                //         Beneficiary::CLAIMED => 'success',

                //         default => 'gray'
                //     }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Beneficiary::STATUS_OPTIONS)->searchable(),
                // SelectFilter::make('distribution_item_id')
                //     ->relationship(
                //         'distributionItem',
                //         fn(Builder $query) => $query,
                //     )
                //     ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->created_at}")

            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                Action::make('SendQr')
                ->label('Send QR to All Beneficiaries')
                ->icon('heroicon-o-paper-airplane')
                ->button()
                // ->hidden(function () {
                //     return !$this->record->beneficiaries()->exists();
                // })
                ->outlined()
                ->requiresConfirmation() // Ask for confirmation before sending
                ->modalHeading('Confirm Sending QR Codes')
                ->modalSubheading('Are you sure you want to send QR codes to all beneficiaries of this distribution?')
                ->action(function (): void {


                    $beneficiaries = Beneficiary::whereHas('distributionItem', function($query){
                        $query->where('distribution_id', $this->record->id);
                    })->get()->filter(function ($beneficiary) {
                        return !empty($beneficiary->email);
                        });



                    foreach ($beneficiaries as $beneficiary) {
                        dispatch(new SendQrMailJob($beneficiary));
                    }


                    Notification::make()
                        ->title('Emails are being sent')
                        ->success()
                        ->send();
                })
                ->closeModalByClickingAway(false)
                ->modalWidth('md'),
            ])
            ->actions([
                Action::make('View Qr')
                        ->color('gray')
                        ->label('View QR Code')
                        ->icon('heroicon-s-eye')

                        ->modalSubmitAction(false)
                        ->modalContent(fn(Model $record): View => view(
                            'livewire.beneficiary-qr',
                            ['record' => $record],
                        ))
                        ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                        ->closeModalByClickingAway(false)->modalWidth('7xl'),
                Action::make('Claim')
                ->requiresConfirmation()
                ->button()
                ->action(function (Model $beneficiary) {
                    try {
                        // Update beneficiary status to 'Claimed'
                        DB::beginTransaction();
                        $beneficiary->update(['status' => Beneficiary::CLAIMED]);


                        // Decrease the quantity in the associated distribution item
                        $distributionItem = $beneficiary->distributionItem;
                        if ($distributionItem && $distributionItem->quantity > 0) {
                            $distributionItem->decrement('quantity');
                        }

                        // Retrieve related details using the passed-in $beneficiary
                        $distributionItemDetails = $beneficiary->distributionItem ?? null;
                        $distributionDetails = $distributionItemDetails?->distribution ?? null;
                        $barangayDetails = $distributionDetails?->barangay ?? null;
                        $supportDetails = Auth::user()->support() ?? null;
                        $currentUser = Auth::user();

                        // Placeholder for recorder details (ensure this is defined or removed as needed)
                        $recorderDetails = []; // TODO: Define recorder details if required

                        // Create a new Transaction record with snapshots of the current state
                        $transaction = Transaction::create([
                            'barangay_id'               => $distributionDetails->barangay_id ?? null,
                            'distribution_id'           => $distributionDetails->id ?? null,
                            'beneficiary_id'            => $beneficiary->id,
                            'distribution_item_id'      => $distributionItemDetails->id ?? null,
                            'support_id'                => $supportDetails->id ?? null,
                            'admin_id'                  => $currentUser->role === User::ADMIN ? $currentUser->id : null,
                            'action'                    => 'Claimed',
                            'performed_at'              => now(),
                            'barangay_details'          => $barangayDetails ? $barangayDetails->toArray() : null,
                            'distribution_details'      => $distributionDetails ? [
                                'id'       => $distributionDetails->id,
                                'title'    => $distributionDetails->title,
                                'location' => $distributionDetails->location,
                                'date'     => $distributionDetails->distribution_date,
                                'code'     => $distributionDetails->code,
                                'status'   => $distributionDetails->status,
                            ] : null,
                            'distribution_item_details' => $distributionItemDetails ? [
                                'id'       => $distributionItemDetails->id,
                                'name'     => $distributionItemDetails->item->name,
                                'quantity' => $distributionItemDetails->quantity,
                            ] : null,
                            'beneficiary_details'       => [
                                'id'      => $beneficiary->id,
                                'name'    => $beneficiary->name,
                                'contact' => $beneficiary->contact,
                                'address' => $beneficiary->address,
                                'email'   => $beneficiary->email,
                                'code'    => $beneficiary->code,
                            ],
                            'support_details'           => $supportDetails ? [
                                'id'          => $supportDetails->id,
                                'name'        => $supportDetails->personnel->user->name,
                                'type'        => $supportDetails->type,
                                'unique_code' => $supportDetails->unique_code,
                            ] : null,
                            'recorder_details'          => $recorderDetails,
                        ]);

                        DB::commit();

                        // Show a success dialog and send a notification

                        Notification::make()
                            ->title('Beneficiary Claimed Successfully')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        report($e);
                        // Optionally, show an error dialog

                        Notification::make()
                        ->title('Claim Failed')
                        ->success()
                        ->send();
                    }
                })
                ->hidden(function (Model $beneficiary) {
                    return $beneficiary->status === Beneficiary::CLAIMED ||
                        !in_array(optional($beneficiary->distributionItem->distribution)->status, [
                            Distribution::STATUS_ONGOING,
                            Distribution::STATUS_COMPLETED,
                        ]);
                })
                ->icon('far-hand-back-fist')
                ->color('success'),

                Action::make('Unclaim')
                ->requiresConfirmation()
                ->button()
                ->action(function (Model $beneficiary) {
                    try {
                        DB::beginTransaction();

                        // Update beneficiary status to 'Unclaimed'
                        $beneficiary->update(['status' => Beneficiary::UN_CLAIMED]);

                        // Increase the quantity in the associated distribution item
                        $distributionItem = $beneficiary->distributionItem;
                        if ($distributionItem) {
                            $distributionItem->increment('quantity');
                        }

                        $currentUser = Auth::user();

                        // Create recorder details for tracing who performed the unclaim action
                        $recorderDetails = [
                            'id'    => $currentUser->id,
                            'name'  => $currentUser->name,
                            'email' => $currentUser->email,
                            'role'  => $currentUser->role,
                        ];

                        // Find the latest transaction with action 'Claimed'
                        $transaction = $beneficiary->transactions()
                            ->where('action', 'Claimed')
                            ->where('beneficiary_id', $beneficiary->id)
                            ->latest()
                            ->first();

                        if ($transaction) {
                            // Update the transaction to reflect the unclaim action and record the details
                            $transaction->update([
                                'action'           => 'Unclaimed',
                                'performed_at'     => now(),
                                'recorder_details' => $recorderDetails,
                            ]);
                        }

                        DB::commit();

                        Notification::make()
                            ->title('Beneficiary Unclaimed Successfully')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        report($e);

                        Notification::make()
                            ->title('Failed to Unclaim Beneficiary')
                            ->danger()
                            ->send();
                    }
                })
                ->hidden(function (Model $beneficiary) {
                    return $beneficiary->status === Beneficiary::UN_CLAIMED ||
                        !in_array(optional($beneficiary->distributionItem->distribution)->status, [
                            Distribution::STATUS_ONGOING,
                            Distribution::STATUS_COMPLETED,
                        ]);
                })
                ->icon('heroicon-o-x-circle')
                ->color('gray')
                ->label('Revert'),


                    ActionGroup::make([
                        Action::make('View Qr')
                            ->color('gray')
                            ->label('View QR Code')
                            ->icon('heroicon-s-eye')
                            ->modalSubmitAction(false)
                            ->modalContent(fn(Model $record): View => view(
                                'livewire.beneficiary-qr',
                                ['record' => $record],
                            ))
                            ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                            ->closeModalByClickingAway(false)->modalWidth('7xl'),

                    ]),





            ])
            ->bulkActions([])
            ->modifyQueryUsing(function ($query) {
                return $query->whereHas('distributionItem', function ($q) {
                    return $q->where('distribution_id', $this->record->id);
                });
            })
        ;
    }
}
