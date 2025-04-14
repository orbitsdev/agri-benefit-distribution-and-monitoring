<?php

namespace App\Filament\Barangay\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use App\Models\BarangayDistribution;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\StaticAction;
use Illuminate\Contracts\View\View;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;
use App\Filament\Barangay\Resources\BarangayDistributionResource\Pages;
use App\Filament\Barangay\Resources\BarangayDistributionResource\RelationManagers;

class BarangayDistributionResource extends Resource
{
    protected static ?string $model = BarangayDistribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationLabel = 'Distributions';
    protected static ?string $navigationGroup = 'OPERATIONS';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form is empty since barangay users cannot modify distributions
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Status first for better visibility
                Tables\Columns\IconColumn::make('is_disbursed')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (Model $record): string => $record->is_disbursed ? 'Disbursed - Beneficiaries can claim benefits' : 'Not Disbursed - Beneficiaries cannot claim benefits yet'),

                // Distribution information
                Tables\Columns\TextColumn::make('distribution.title')
                    ->label('Distribution Title')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('distribution_date')
                    ->label('Distribution Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Venue')
                    ->searchable(),

                // Beneficiary statistics
                // Tables\Columns\TextColumn::make('beneficiaries_count')
                //     ->label('Total Beneficiaries')
                //     ->counts('beneficiaries')
                //     ->sortable(),


                ProgressBar::make('progress_percentage')
                ->label('Claim Progress')
                ->tooltip('This progress bar represents the completion status of beneficiary claims. It shows how many beneficiaries have claimed their benefits and how many remain.')
                ->width('30%')
                ->getStateUsing(function (Model $record) {
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

                // Tables\Columns\TextColumn::make('claimed_beneficiaries_count')
                //     ->label('Claimed')
                //     ->counts('beneficiaries', function (Builder $query) {
                //         $query->whereHas('cropsToReceive', function ($query) {
                //             $query->where('is_claimed', true);
                //         });
                //     })
                //     ->color('success'),

                // Tables\Columns\TextColumn::make('unclaimed_beneficiaries_count')
                //     ->label('Unclaimed')
                //     ->counts('beneficiaries', function (Builder $query) {
                //         $query->whereHas('cropsToReceive', function ($query) {
                //             $query->where('is_claimed', false);
                //         });
                //     })
                //     ->color('danger'),

                // Less important information hidden by default
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // No filters needed for barangay view
            ])
            ->actions([
                // View-only actions


                ActionGroup::make([
                    Tables\Actions\Action::make('View')
                        ->label('View Details')
                        ->icon('heroicon-s-eye')
                        ->color('gray')
                        ->modalSubmitAction(false)
                        ->modalContent(fn(Model $record): View => view(
                            'livewire.view-barangay-distribution',
                            ['record' => $record],
                        ))
                        ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                        ->closeModalByClickingAway(false)
                        ->modalWidth('7xl'),

                    Tables\Actions\Action::make('Beneficiaries')
                        ->label('List Of Beneficiaries')
                        ->icon('heroicon-o-user-group')
                        ->color('gray')
                        ->url(function (Model $record) {
                            return BarangayDistributionResource::getUrl('beneficiaries', ['record' => $record->id]);
                        }, shouldOpenInNewTab: true)
                        ->visible(fn (Model $record) => $record->is_disbursed),

                   // Report Export Actions
                   Tables\Actions\Action::make('Transaction Report')
                       ->size(ActionSize::ExtraSmall)
                       ->label('Transaction Report')
                       ->icon('heroicon-s-arrow-down-tray')
                       ->url(function (Model $record) {
                           return route('export.barangay.transactions', ['record' => $record->id]);
                       }, shouldOpenInNewTab: true)
                       ->hidden(function (Model $record) {
                           return !Transaction::where('barangay_distribution_id', $record->id)->exists();
                       }),

                   Tables\Actions\Action::make('All Beneficiaries')
                       ->size(ActionSize::ExtraSmall)
                       ->label('All Beneficiaries')
                       ->icon('heroicon-s-arrow-down-tray')
                       ->url(function (Model $record) {
                           return route('export.barangay.beneficiaries', [
                               'barangayDistribution' => $record->id,
                               'filter' => 'all'
                           ]);
                       }, shouldOpenInNewTab: true)
                       ->hidden(function (Model $record) {
                           return !$record->beneficiaries()->exists();
                       }),

                   Tables\Actions\Action::make('Claimed Beneficiaries')
                       ->size(ActionSize::ExtraSmall)
                       ->label('Claimed Beneficiaries')
                       ->icon('heroicon-s-arrow-down-tray')
                       ->url(function (Model $record) {
                           return route('export.barangay.beneficiaries', [
                               'barangayDistribution' => $record->id,
                               'filter' => 'claimed'
                           ]);
                       }, shouldOpenInNewTab: true)
                       ->hidden(function (Model $record) {
                           return !$record->beneficiaries()
                               ->whereHas('cropsToReceive', function($query) {
                                   $query->where('is_claimed', true);
                               })->exists();
                       }),

                   Tables\Actions\Action::make('Unclaimed Beneficiaries')
                       ->size(ActionSize::ExtraSmall)
                       ->label('Unclaimed Beneficiaries')
                       ->icon('heroicon-s-arrow-down-tray')
                       ->url(function (Model $record) {
                           return route('export.barangay.beneficiaries', [
                               'barangayDistribution' => $record->id,
                               'filter' => 'unclaimed'
                           ]);
                       }, shouldOpenInNewTab: true)
                       ->hidden(function (Model $record) {
                           return !$record->beneficiaries()
                               ->whereHas('cropsToReceive', function($query) {
                                   $query->where('is_claimed', false);
                               })->exists();
                       }),

                   Tables\Actions\Action::make('Inventory Report')
                       ->size(ActionSize::ExtraSmall)
                       ->label('Inventory Report')
                       ->icon('heroicon-s-arrow-down-tray')
                       ->url(function (Model $record) {
                           return route('export.barangay.crops', [
                               'barangayDistribution' => $record->id
                           ]);
                       }, shouldOpenInNewTab: true)
                       ->hidden(function (Model $record) {
                           return !$record->beneficiaries()
                               ->whereHas('cropsToReceive')
                               ->exists();
                       }),

                //     Action::make('view_details')
                //         ->label('View Details')
                //         ->icon('heroicon-o-eye')
                //         ->modalSubmitAction(false)
                //         ->modalContent(fn(Model $record): View => view(
                //             'livewire.view-barangay-distribution',
                //             ['record' => $record],
                //         ))
                //         ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                //         ->closeModalByClickingAway(false)
                //         ->modalWidth('7xl'),

                //     Action::make('export_beneficiaries')
                //         ->label('Export Beneficiaries')
                //         ->icon('heroicon-o-arrow-down-tray')
                //         ->url(fn (Model $record) => route('export.beneficiaries', [
                //             'barangay_distribution' => $record->id,
                //             'filter' => 'all'
                //         ]))
                //         ->visible(fn (Model $record) => $record->beneficiaries()->exists()),

                //     Action::make('export_claimed')
                //         ->label('Export Claimed')
                //         ->icon('heroicon-o-arrow-down-tray')
                //         ->size(ActionSize::ExtraSmall)
                //         ->url(fn (Model $record) => route('export.beneficiaries', [
                //             'barangay_distribution' => $record->id,
                //             'filter' => 'claimed'
                //         ]))
                //         ->visible(fn (Model $record) => $record->beneficiaries()
                //             ->whereHas('cropsToReceive', function($query) {
                //                 $query->where('is_claimed', true);
                //             })->exists()),

                //     Action::make('export_unclaimed')
                //         ->label('Export Unclaimed')
                //         ->icon('heroicon-o-arrow-down-tray')
                //         ->size(ActionSize::ExtraSmall)
                //         ->url(fn (Model $record) => route('export.beneficiaries', [
                //             'barangay_distribution' => $record->id,
                //             'filter' => 'unclaimed'
                //         ]))
                //         ->visible(fn (Model $record) => $record->beneficiaries()
                //             ->whereHas('cropsToReceive', function($query) {
                //                 $query->where('is_claimed', false);
                //             })->exists()),
                ]),
            ])
            ->bulkActions([
                // No bulk actions needed for barangay view
            ])
            ->modifyQueryUsing(function (Builder $query) {
                // Only show distributions for the authenticated user's barangay
                // and where the distribution is disbursed
                return $query->where('barangay_id', Auth::user()->barangay_id)
                             ->where('is_disbursed', true);
            })
            ->defaultSort('distribution_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // No relations needed for barangay view
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangayDistributions::route('/'),
            'beneficiaries' => Pages\ListOfBarangayBeneficiaries::route('/{record}/beneficiaries'),
            'transaction-history' => Pages\TransactionHistory::route('/{record}/transaction-history'),
        ];
    }
}
