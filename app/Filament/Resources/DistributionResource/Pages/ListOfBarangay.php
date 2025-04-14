<?php

namespace App\Filament\Resources\DistributionResource\Pages;

use Filament\Tables\Table;
use App\Models\Distribution;

use Filament\Resources\Pages\Page;
use App\Models\BarangayDistribution;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;

use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\DistributionResource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Resources\Pages\ManageRelatedRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ActionGroup;
class ListOfBarangay extends ManageRelatedRecords {

    use NestedPage;
    use NestedRelationManager;
    protected static string $resource = DistributionResource::class;

    // protected static string $view = 'filament.resources.distribution-resource.pages.list-of-barangay';
    protected static ?string $navigationIcon = 'hugeicons-city-01';

    protected static string $relationship = 'barangay_distributions';

    protected ?string $heading = 'Barangay';
    public static function getNavigationLabel(): string
    {
        return 'Barangay';
    }


    protected function getDistribution(): Model
    {
        return $this->getOwnerRecord();
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(BarangayDistribution::query())
            ->columns([
                TextColumn::make('barangay.name')->searchable()->label('Barangay'),
            ])
            ->filters([

            ], layout: FiltersLayout::AboveContent)

            ->actions([
                \Filament\Tables\Actions\ActionGroup::make([
                    // \Filament\Tables\Actions\ViewAction::make(),
                    \Filament\Tables\Actions\EditAction::make()->label('Manage'),
                    \Filament\Tables\Actions\DeleteAction::make()->color('gray'),
                ])->icon('heroicon-m-ellipsis-vertical')
            ])

        ->bulkActions([])
            // ->modifyQueryUsing(function (Builder $query) {
            //     return $query->where('distribution_id',$this->getRecord()->id);
            // })
        ;
    }
}
