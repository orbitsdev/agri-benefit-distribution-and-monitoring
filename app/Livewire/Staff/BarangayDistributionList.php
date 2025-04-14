<?php

namespace App\Livewire\Staff;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Tables\Table;
use WireUi\Traits\WireUiActions;
use App\Models\BarangayDistribution;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;

class BarangayDistributionList extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use WireUiActions;

    protected function table(Table $table): Table
    {
        return $table
            ->query(
                BarangayDistribution::query()
                    ->where('barangay_id', Auth::user()->barangay_id)
                    ->where('is_disbursed', true)
            )
            ->columns([
                // Status first for better visibility
                IconColumn::make('is_disbursed')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record): string => $record->is_disbursed ? 'Disbursed - Beneficiaries can claim benefits' : 'Not Disbursed - Beneficiaries cannot claim benefits yet'),

                // Distribution information
                TextColumn::make('distribution.title')
                    ->label('Distribution Title')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('distribution_date')
                    ->label('Distribution Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('location')
                    ->label('Venue')
                    ->searchable(),

                // Beneficiary claim progress
                ProgressBar::make('progress_percentage')
                    ->label('Claim Progress')
                    ->tooltip('This progress bar represents the completion status of beneficiary claims')
                    ->width('30%')
                    ->getStateUsing(function ($record) {
                        // Count total beneficiaries and claimed beneficiaries for this barangay distribution
                        $totalBeneficiaries = $record->beneficiaries()->count();
                        $claimedBeneficiaries = $record->beneficiaries()
                            ->whereHas('cropsToReceive', function ($query) {
                                $query->where('is_claimed', true);
                            })
                            ->count();

                        return [
                            'total' => $totalBeneficiaries,
                            'progress' => $claimedBeneficiaries,
                            'remaining' => $totalBeneficiaries - $claimedBeneficiaries,
                        ];
                    })->hideProgressValue(false),
            ])
            ->filters([
                // No filters needed for now
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view_beneficiaries')
                        ->icon('heroicon-o-eye')
                        ->label('View Distribution Details')
                        ->color('gray')
                        ->url(fn ($record) => route('staff.distribution.details', ['distribution' => $record->id])),
                ]),

                // Tables\Actions\Action::make('scan_qr')
                //     ->label('Scan QR')
                //     ->icon('heroicon-o-qr-code')
                //     ->color('success')
                //     ->url(fn ($record) => route('staff.qr-scanner', ['distribution' => $record->id]))
                //     ->openUrlInNewTab(),
            ])
            ->emptyStateIcon('heroicon-o-information-circle')
            ->emptyStateHeading('No Active Distributions Found')
            ->emptyStateDescription('There are no active distributions for your barangay at this time.')
            ->defaultSort('distribution_date', 'desc');
    }

    public function render()
    {
        return view('livewire.staff.barangay-distribution-list');
    }
}
