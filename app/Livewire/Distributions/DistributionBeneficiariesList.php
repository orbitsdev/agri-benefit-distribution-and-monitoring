<?php

namespace App\Livewire\Distributions;

use Filament\Tables;
use App\Models\Support;
use Livewire\Component;
use Filament\Tables\Table;
use App\Models\Beneficiary;
use App\Models\Distribution;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class DistributionBeneficiariesList extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

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
                    $beneficiary->update(['status' => Beneficiary::CLAIMED]);

                    // Decrease the quantity in the associated distribution item
                    $distributionItem = $beneficiary->distributionItem;
                    if ($distributionItem->quantity > 0) {
                        $distributionItem->decrement('quantity');
                    }

                    $adminId = null;
                    $support = $this->support;

                    // Create a transaction record with JSON snapshots
                    $beneficiary->transactions()->create([
                        'barangay_id' => $distributionItem->distribution->barangay_id,
                        'distribution_id' => $distributionItem->distribution_id,
                        'distribution_item_id' => $distributionItem->id,
                        'beneficiary_id' => $beneficiary->id,
                        'support_id' => $beneficiary->support_id,
                        'admin_id' => $adminId,

                        // JSON Snapshots (Storing full details)
                        'barangay_details' => [
                            'id' => $distributionItem->distribution->barangay_id,
                            'name' => $distributionItem->distribution->barangay->name,
                            'location' => $distributionItem->distribution->barangay->location,
                        ],

                        'distribution_details' => [
                            'id' => $distributionItem->distribution_id,
                            'title' => $distributionItem->distribution->title,
                            'location' => $distributionItem->distribution->location,
                            'date' => $distributionItem->distribution->distribution_date,
                            'code' => $distributionItem->distribution->code,
                        ],

                        'distribution_item_details' => [
                            'id' => $distributionItem->id,
                            'name' => $distributionItem->item->name,
                        ],

                        'beneficiary_details' => [
                            'id' => $beneficiary->id,
                            'name' => $beneficiary->name,
                            'contact' => $beneficiary->contact,
                            'address' => $beneficiary->address,
                            'email' => $beneficiary->email,
                            'code' => $beneficiary->code,
                        ],

                        'support_details' => [
                            'id' => $support->id,
                            'name' => $support->personnel->user->name,
                            'type' => $support->type,
                            'unique_code' => $support->unique_code,
                        ],

                        // Action Details
                        'action' => 'Claimed',
                        'performed_at' => now(),
                    ]);

                    $this->dispatch('refreshProgress');

                    // Send success notification
                    Notification::make()
                        ->title('Beneficiary Claimed Successfully')
                        ->success()
                        ->send();
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
                    $beneficiary->update(['status' => Beneficiary::UN_CLAIMED]);

                        // Increase the quantity in the associated distribution item
                        $distributionItem = $beneficiary->distributionItem;
                        if ($distributionItem) {
                            $distributionItem->increment('quantity');
                        }

                        // Find and update the associated transaction instead of deleting it
                        $transaction = $beneficiary->transactions()
                            ->where('action', 'Claimed')
                            ->where('beneficiary_id', $beneficiary->id)
                            ->latest()
                            ->first();

                        if ($transaction) {
                            $transaction->update([
                                'action' => 'Unclaimed',
                                'performed_at' => now(), // Log the unclaim action
                            ]);
                        }
                        $this->dispatch('refreshProgress');

                        Notification::make()
                            ->title('Beneficiary Unclaimed Successfully')
                            ->success()
                            ->send();
                })
                ->hidden(function (Model $beneficiary) {
                    return $beneficiary->status === Beneficiary::UN_CLAIMED ||
                    !in_array(optional($beneficiary->distributionItem->distribution)->status, [Distribution::STATUS_ONGOING, Distribution::STATUS_COMPLETED]);
                })
                // ->icon('heroicon-o-x-circle')
                ->color('gray')
                ->label('Return'),
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
