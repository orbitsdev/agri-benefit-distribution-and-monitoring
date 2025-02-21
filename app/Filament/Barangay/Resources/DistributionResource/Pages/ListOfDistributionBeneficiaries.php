<?php

namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use Filament\Tables\Table;
use App\Models\Beneficiary;
use App\Models\Distribution;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

use App\Filament\Barangay\Resources\DistributionResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

use Illuminate\Database\Eloquent\Builder;

class ListOfDistributionBeneficiaries extends Page  implements HasForms, HasTable

{

    use InteractsWithTable;
    use InteractsWithForms;
    protected static string $resource = DistributionResource::class;

    protected static string $view = 'filament.barangay.resources.distribution-resource.pages.list-of-distribution-beneficiaries';

    public $record;

    public function mount(Distribution $record): void {
        $this->record = $record;
    }

    public function getHeading(): string
    {
        $item = $this->record->title;
        return __($item . ' Benificiaries');
    }

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
                // SelectFilter::make('distribution_item_id')
                //     ->relationship(
                //         'distributionItem',
                //         fn(Builder $query) => $query,
                //     )
                //     ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->created_at}")

            ], layout: FiltersLayout::AboveContent)
            ->headerActions([])
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
                        // Create a transaction record
                        $beneficiary->transactions()->create([
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



                            'action' => 'Claimed',
                            'performed_at' => now(),
                        ]);

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
            ->bulkActions([])
            ->modifyQueryUsing(function ($query) {
                return $query->whereHas('distributionItem', function($q){
                    return $q->where('distribution_id', $this->record->id);
                } );
            })
        ;
    }
}
