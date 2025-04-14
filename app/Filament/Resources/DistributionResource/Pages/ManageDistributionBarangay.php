<?php


namespace App\Filament\Resources\DistributionResource\Pages;


use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;

use Illuminate\Database\Eloquent\Model;

use Filament\Tables\Actions\ActionGroup;

use Illuminate\Database\Eloquent\Builder;

use App\Filament\Resources\DistributionResource;
use Filament\Resources\Pages\ManageRelatedRecords;

use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;

class ManageDistributionBarangay extends ManageRelatedRecords
{
    use NestedPage;
    use NestedRelationManager;

    protected static string $resource = DistributionResource::class;

    protected static string $relationship = 'barangayDistributions';

    protected static ?string $navigationIcon = 'hugeicons-city-01';

    protected static ?string $navigationLabel = 'Barangay';

    protected ?string $heading = 'Barangay';

     // public static function getNavigationLabel(): string
    // {
    //     return 'Distribution Items';
    // }

    public static function getNavigationLabel(): string
    {
        return 'Barangay';
    }

    // protected function getDistribution(): Model
    // {
    //     return $this->getOwnerRecord();
    // }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item_id')
            ->columns([
                TextColumn::make('barangay.name')->searchable()->label('Barangay'),
                TextColumn::make('beneficiaries_count')
                    ->label('Total Beneficiaries')
                    ->counts('beneficiaries')
                    ->alignCenter()

            ])
            ->filters([
                //
            ])

            ->actions([


                Tables\Actions\EditAction::make()->label('Beneficiaries / Crops')->button()->tooltip('Manage Beneficiaries & Crops'),


                // ActionGroup::make([

                //     // Tables\Actions\ViewAction::make(),

                //     // Tables\Actions\DeleteAction::make()->color('gray'),

                // ]),
            ])
            ->bulkActions([

            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('distribution_id', $this->getRecord()->id);
            })->paginated(false)
        ;
    }
}
