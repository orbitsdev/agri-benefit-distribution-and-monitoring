<?php

namespace App\Livewire\Distributions;

use App\Models\User;
use Filament\Tables;
use App\Models\Support;
use Livewire\Component;
use Filament\Tables\Table;
use App\Models\Beneficiary;
use App\Models\Distribution;
use WireUi\Traits\WireUiActions;
use Filament\Actions\StaticAction;
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

class DistributionBeneficiariesList extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use WireUiActions;
    public $record;
    public $support;

    public function mount(){
        $this->support = Support::where('unique_code', Auth::user()->code)->first() ;
      $this->record = $this->support->distribution;
    //   dd($this->record);

    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Beneficiary::query())
            ->columns([
                // ✅ Improved label
                ViewColumn::make('qr')
                ->view('tables.columns.beneficiary-qr')
                ->label('QR Code'), // ✅ Improved label
                TextColumn::make('code')
                ->label('Beneficiary Code') // ✅ Clearer label
                ->searchable(isIndividual:true),
            TextColumn::make('name')
                ->label('Beneficiary Name') // ✅ Clearer label
                ->searchable(isIndividual:true),


            TextColumn::make('contact')
                ->label('Contact Number') // ✅ More descriptive
                ->searchable(),

            TextColumn::make('email')
                ->label('Email Address')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('address')
                ->label('Home Address')
                ->searchable()
                ->wrap(),

            TextColumn::make('distributionItem.item.name')
                ->label('Item Received') // ✅ More intuitive
                ->searchable(),

                TextColumn::make('status')
                ->label('Claim Status') // ✅ More descriptive
                ->badge()
                ->icon(fn(string $state): string => match ($state) {  // ✅ Add icon based on status
                    Beneficiary::CLAIMED => 'heroicon-o-check', // ✅ Success Icon
                    default => '', // ❌ Default (Unclaimed)
                })
                ->color(fn(string $state): string => match ($state) {
                    Beneficiary::CLAIMED => 'success',
                    default => 'gray'
                }),



            ])
            ->filters([
                 SelectFilter::make('status')
                    ->options(Beneficiary::STATUS_OPTIONS)->searchable(),
            ], layout: FiltersLayout::Modal)
            ->actions([
                Action::make('Claim')
                ->requiresConfirmation()
                ->button()
                ->action(function (Model $beneficiary) {

                    try {
                    $beneficiary->update(['status' => Beneficiary::CLAIMED]);
                              DB::beginTransaction();
                    // Decrease the quantity in the associated distribution item
                    $distributionItem = $beneficiary->distributionItem;
                    if ($distributionItem->quantity > 0) {
                        $distributionItem->decrement('quantity');
                    }

                     $distributionItemDetails = $beneficiary->distributionItem ?? null;
                        $distributionDetails = $distributionItemDetails?->distribution ?? null;
                        $barangayDetails = $distributionDetails?->barangay ?? null;
                        $supportDetails =  $this->support ?? null;
                        $currentUser = Auth::user();
                         $recorderDetails = [];


                    // Create a transaction record with JSON snapshots
                    $beneficiary->transactions()->create([
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

                    $this->dispatch('refreshProgress');

                    // Send success notification
                    $this->dialog()->success(
                        title: 'Beneficiary Claimed',
                        description: 'The beneficiary has been successfully marked as claimed.'
                    );

                } catch (\Exception $e) {
                    DB::rollBack();
                    report($e);
                    // Optionally, show an error dialog


                     $this->dialog()->error(
                        title: 'Claim Failed',
                        description: 'Trasaction Failed to create.'.$e->getMessage()
                    );
                }

                })
                ->hidden(function (Model $beneficiary) {
                    return $beneficiary->status === Beneficiary::CLAIMED ||
                    !in_array(optional($beneficiary->distributionItem->distribution)->status, [Distribution::STATUS_ONGOING, Distribution::STATUS_COMPLETED]);
                })
                // ->icon('far-hand-back-fist')
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
                        $this->dispatch('refreshProgress');
                        $this->dialog()->success(
                            title: 'Beneficiary Unclaimed',
                            description: 'The beneficiary claim has been successfully reverted.',
                        );


                    } catch (\Exception $e) {
                        DB::rollBack();
                        report($e);

                        $this->dialog()->error(
                            title: 'Beneficiary Unclaimed Failed',
                            description: 'Error'.$e->getMessage()
                        );
                    }
                })
                ->hidden(function (Model $beneficiary) {
                    return $beneficiary->status === Beneficiary::UN_CLAIMED ||
                    !in_array(optional($beneficiary->distributionItem->distribution)->status, [Distribution::STATUS_ONGOING, Distribution::STATUS_COMPLETED]);
                })
                // ->icon('heroicon-o-x-circle')
                ->color('gray')
                ->label('Return'),
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->whereHas('distributionItem', function($query){
                    return $query->where('distribution_id', $this->record->id);
                });
            });
    }

    public function getProgressDataProperty()
{
    $total = Beneficiary::whereHas('distributionItem', function($query) {
        return $query->where('distribution_id', $this->record->id);
    })->count();

    $claimed = Beneficiary::whereHas('distributionItem', function($query) {
        return $query->where('distribution_id', $this->record->id);
    })->where('status', Beneficiary::CLAIMED)->count();

    $progressPercent = $total > 0 ? round(($claimed / $total) * 100, 2) : 0;

    return [
        'total' => $total,
        'claimed' => $claimed,
        'remaining' => $total - $claimed,
        'percentage' => $progressPercent,
    ];
}


    public function render(): View
    {
        return view('livewire.distributions.distribution-beneficiaries-list');
    }
}
