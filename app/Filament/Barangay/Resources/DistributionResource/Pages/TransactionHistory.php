<?php

namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use App\Models\Distribution;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Barangay\Resources\DistributionResource;
use App\Models\Transaction;
use Filament\Tables\Table;
class TransactionHistory extends Page  implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = DistributionResource::class;

    protected static string $view = 'filament.barangay.resources.distribution-resource.pages.transaction-history';

    public $record;
    public function mount(Distribution $record): void {}

    public function getHeading(): string
    {
        $item = $this->record->title;
        return __($item . 'Transaction History');
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(Transaction::query())
            ->columns([

            ])
            ->filters([

            ], layout: FiltersLayout::AboveContent)
            ->headerActions([])
            ->actions([





            ])
            ->bulkActions([])
            ->modifyQueryUsing(function ($query) {
                return $query;
            })
        ;
    }

}
