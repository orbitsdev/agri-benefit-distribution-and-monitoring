<?php

namespace App\Filament\Barangay\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Distribution;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Widgets\TableWidget as BaseWidget;
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
                    ->searchable()->wrap(),
                    Tables\Columns\TextColumn::make('code')
                    ->copyable()
                    ->searchable()->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('distribution_date')
                    ->date()
                    ->label('Date')
                    ->sortable()->toggleable(),
                    Tables\Columns\TextColumn::make('location')
                    ->searchable()->label('Location/Venue')->wrap()->toggleable(),
                    Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        Distribution::STATUS_PLANNED => 'gray',
                        Distribution::STATUS_ONGOING => 'success',
                        Distribution::STATUS_COMPLETED=> 'success',
                        Distribution::STATUS_CANCELED=> 'danger',

                        default => 'gray'
                    }),

                    ProgressBar::make('progress_percentage')
                    ->label('Progress')
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
            ->modifyQueryUsing(fn($query)=> $query->limit(10))
            ->poll('20s')
            ;
    }
}
