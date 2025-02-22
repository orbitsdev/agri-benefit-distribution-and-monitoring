<?php

namespace App\Filament\Barangay\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Distribution;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\Barangay\Resources\DistributionResource;
use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;

class LatestDistributions extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 5;
    public function table(Table $table): Table
    {
        $barangay_id = Auth::user()->barangay_id;
        return $table
        ->query(Distribution::query()->latest()->byBarangay($barangay_id)->locked()->ongoingOrCompleted())
        ->heading('Latest Distributions')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()->wrap()->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        Distribution::STATUS_PLANNED => 'gray',
                        Distribution::STATUS_ONGOING => 'success',
                        Distribution::STATUS_COMPLETED=> 'success',
                        Distribution::STATUS_CANCELED=> 'danger',

                        default => 'gray'
                    }),
                    Tables\Columns\TextColumn::make('code')
                    ->copyable()
                    ->searchable()->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('distribution_date')
                    ->date()
                    ->label('Date')
                    ->sortable()->toggleable(isToggledHiddenByDefault: false),

                    Tables\Columns\TextColumn::make('location')
                    ->searchable()->label('Location/Venue')->wrap()->toggleable(),


                    ProgressBar::make('progress_percentage')
                    ->label('Progress')
                    ->tooltip('This progress bar represents the completion status of beneficiary distribution. It helps monitor how many beneficiaries have claimed their items and how much remains to be completed.')
                ->getStateUsing(function (Model $record) {
                    return [
                        'total' => $record->total_beneficiaries,
                        'progress' => $record->claimed_beneficiaries,
                        'remaining' => $record->total_beneficiaries - $record->claimed_beneficiaries,
                    ];
                })
                ->hideProgressValue(true),

                    // ViewColumn::make('progress')->view('tables.columns.distribution-progress')

            ])
            ->actions([
                ActionGroup::make([

                    Action::make('Beneficiaries') // Disable closing the modal by clicking outside


                    ->label('Beneficiaries') // Add label for better UX
                    ->icon('heroicon-s-eye') // Optional: Add an icon for better UI
                    ->url(function (Model $record) {

                      return DistributionResource::getUrl('distribution-beneficiaries',['record'=>$record->id]);

                    }, shouldOpenInNewTab: true)
                  ,
 Action::make('Transaction') // Disable closing the modal by clicking outside



                    ->label('Transaction History') // Add label for better UX
                    ->icon('heroicon-s-clock')
                    ->url(function (Model $record) {

                      return DistributionResource::getUrl('distribution-transaction-history',['record'=>$record->id]);

                    }, shouldOpenInNewTab: true)
                  ,
                ]),

            ])
            ->modifyQueryUsing(fn($query)=> $query->limit(10))
            // ->poll('20s')
            ;
    }
}
