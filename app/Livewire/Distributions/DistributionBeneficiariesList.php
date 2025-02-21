<?php

namespace App\Livewire\Distributions;

use Filament\Tables;
use Livewire\Component;
use Filament\Tables\Table;
use App\Models\Beneficiary;
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



    public function table(Table $table): Table
    {
        return $table
            ->query(Beneficiary::query())
            ->columns([
                ViewColumn::make('code')->view('tables.columns.beneficiary-qr'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault:true),
                TextColumn::make('contact')->searchable(),
                TextColumn::make('address')->searchable()->wrap(),
                TextColumn::make('distributionItem.item.name')->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        Beneficiary::CLAIMED => 'success',
                        default => 'gray'
                    }),
            ])
            ->filters([
                 SelectFilter::make('status')
                    ->options(Beneficiary::STATUS_OPTIONS)->searchable(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Action::make('Claim')
                ->requiresConfirmation()
                ->button()
                ->action(function (Model $beneficiary) {
                    // Update beneficiary status to 'Claimed'
                    $beneficiary->update(['status' => Beneficiary::CLAIMED]);


                    // Decrease the quantity in the associated distribution item
                    $distributionItem = $beneficiary->distributionItem;
                    if ($distributionItem->quantity > 0) {
                        $distributionItem->decrement('quantity');
                    }
                    $adminId = Auth::user()->role === 'admin' ? Auth::user()->id : null;
                    $support = Auth::user()->support();

                    // Create a transaction record
                    $transactionData = [
                        'barangay_id' => $distributionItem->distribution->barangay_id,
                        'barangay_name' => $distributionItem->distribution->barangay->name,
                        'barangay_location' => $distributionItem->distribution->barangay->location,

                        'distribution_id' => $distributionItem->distribution_id,
                        'distribution_title' => $distributionItem->distribution->title,
                        'distribution_location' => $distributionItem->distribution->location,
                        'distribution_date' => $distributionItem->distribution->distribution_date,
                        'distribution_code' => $distributionItem->distribution->code,

                        'distribution_item_id' => $distributionItem->id,
                        'distribution_item_name' => $distributionItem->item->name,

                        'beneficiary_id' => $beneficiary->id,
                        'beneficiary_name' => $beneficiary->name,
                        'beneficiary_contact' => $beneficiary->contact,
                        'beneficiary_email' => $beneficiary->email,
                        'beneficiary_code' => $beneficiary->code,
                        'admin_id' => $adminId,

                        'support_id' => $support->id,
                        'support_name' => Auth::user()->name,
                        'support_type' => $support->type,
                        'support_unique_code' => $support->unique_code,



                        'action' => 'Claimed',
                        'performed_at' => now(),
                    ];

                    $beneficiary->transactions()->create($transactionData);

                    Notification::make()
                        ->title('Beneficiary Claimed Successfully')
                        ->success()
                        ->send();
                })
                ->hidden(function (Model $beneficiary) {
                    return $beneficiary->status === Beneficiary::CLAIMED;
                })
                ->icon('far-hand-back-fist')
                ->color('success'),
            Action::make('Unclaim')
                ->requiresConfirmation()
                ->button()
                ->action(function (Model $beneficiary) {
                    // Update beneficiary status to 'Unclaimed'
                    $beneficiary->update(['status' => Beneficiary::UN_CLAIMED]);

                    // Increase the quantity in the associated distribution item
                    $distributionItem = $beneficiary->distributionItem;
                    $distributionItem->increment('quantity');


                    // Delete the associated transaction for this beneficiary
                    $beneficiary->transactions()
                        ->where('action', 'Claimed')
                        ->where('beneficiary_id', $beneficiary->id)
                        ->delete();

                    Notification::make()
                        ->title('Beneficiary Unclaimed Successfully')
                        ->success()
                        ->send();
                })
                ->hidden(function (Model $beneficiary) {
                    return $beneficiary->status === Beneficiary::UN_CLAIMED;
                })
                ->icon('heroicon-o-x-circle')
                ->color('gray')
                ->label('Revert'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query;
            });
    }

    public function render(): View
    {
        return view('livewire.distributions.distribution-beneficiaries-list');
    }
}
