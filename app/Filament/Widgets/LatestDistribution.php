<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Distribution;
use Filament\Actions\StaticAction;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Filament\Support\Enums\ActionSize;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\Resources\DistributionResource;
use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;

class LatestDistribution extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Distribution::query()
                    ->where('is_disbursed', true)
                    ->latest()
            )
            ->heading('Latest Distributions')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('distribution_date')
                    ->date()
                    ->label('Date')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('is_disbursed')
                    ->label('Disbursement')
                    ->badge()
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Disbursed' : 'Not Disbursed')
                    ->icon(fn(bool $state): string => $state ? 'heroicon-o-banknotes' : 'heroicon-o-x-circle')
                    ->color(fn(bool $state): string => $state ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('barangay_distributions_count')
                    ->counts('barangay_distributions')
                    ->label('Total Barangay')
                    ->badge()
                    ->color('primary'),

                ProgressBar::make('progress_percentage')
                    ->label('Claim Progress')
                    ->tooltip('This progress bar represents the completion status of beneficiary claims. It shows how many beneficiaries have claimed their benefits and how many remain.')
                    ->width('30%')
                    ->getStateUsing(function (Model $record) {
                        // Count total beneficiaries and claimed beneficiaries across all barangay distributions
                        $totalBeneficiaries = 0;
                        $claimedBeneficiaries = 0;

                        foreach ($record->barangayDistributions as $barangayDistribution) {
                            foreach ($barangayDistribution->beneficiaries as $beneficiary) {
                                $totalBeneficiaries++;

                                $cropToReceive = $beneficiary->cropsToReceive;
                                if ($cropToReceive && $cropToReceive->is_claimed) {
                                    $claimedBeneficiaries++;
                                }
                            }
                        }

                        return [
                            'total' => $totalBeneficiaries,
                            'progress' => $claimedBeneficiaries,
                            'remaining' => $totalBeneficiaries - $claimedBeneficiaries,
                        ];
                    })->hideProgressValue(true),

                // Tables\Columns\TextColumn::make('barangayDistributions.barangay.name')
                //     ->label('Barangays')
                //     ->listWithLineBreaks()
                //     ->limitList(3)
                //     ->expandableLimitedList(),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('View')
                        ->label('View Details')
                        ->icon('heroicon-s-eye')
                        ->modalSubmitAction(false)
                        ->modalContent(fn(Model $record): View => view(
                            'livewire.view-distribution',
                            ['record' => $record],
                        ))
                        ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                        ->closeModalByClickingAway(false)
                        ->modalWidth('7xl'),

                    Action::make('Beneficiaries')
                        ->label('List Of Beneficiaries')
                        ->icon('heroicon-s-user-group')
                        ->url(function (Model $record) {
                            return DistributionResource::getUrl('beneficiaries', ['record' => $record->id]);
                        }, shouldOpenInNewTab: true),

                    Action::make('Barangays')
                        ->label('Manage Barangays')
                        ->icon('heroicon-s-home-modern')
                        ->url(function (Model $record) {
                            return DistributionResource::getUrl('barangayDistributions', ['record' => $record->id]);
                        }, shouldOpenInNewTab: true),

                    Tables\Actions\Action::make('Transaction')
                        ->size(ActionSize::ExtraSmall)
                        ->label('Transaction History')
                        ->icon('heroicon-s-clock')
                        ->url(function (Model $record) {
                            return DistributionResource::getUrl('transaction-history', ['record' => $record->id]);
                        }, shouldOpenInNewTab: true)
                        ->hidden(function (Model $record) {
                            return !$record->transactions()->exists();
                        }),
                ]),
            ])
            ->modifyQueryUsing(fn($query) => $query->limit(10));
    }
}
