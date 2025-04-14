<?php


namespace App\Filament\Resources\DistributionResource\Pages;


use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Crop;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DistributionResource;

use Filament\Resources\Pages\ManageRelatedRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;

class ManageDistributionCrop extends ManageRelatedRecords
{
    // use NestedPage;
    // use NestedRelationManager;

    protected static string $resource = DistributionResource::class;

    protected static string $relationship = 'crops';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Distribution Crops';

    protected ?string $heading = 'Manage  Crops';

     // public static function getNavigationLabel(): string
    // {
    //     return 'Distribution Items';
    // }

    public static function getNavigationLabel(): string
    {
        return 'Crops';
    }

    protected function getDistribution(): Model
    {
        return $this->getOwnerRecord();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->required()
                ->maxLength(191)
                ->columnSpanFull(),

            // Original Stocks Field
            TextInput::make('original_stocks')
                ->numeric()
                ->default(1)
                ->required()
                ->columnSpan([
                    'sm' => 2,
                    'md' => 2,
                    'lg' => 2,
                ]),
                // Section::make('Crop Details')
                //     ->description('Specify the crop details.')
                //     ->columns([
                //         'sm' => 2,
                //         'md' => 4,
                //         'lg' => 6,
                //         'xl' => 8,
                //         '2xl' => 12,
                //     ])
                //     ->columnSpanFull()
                //     ->schema([
                //         // Crop Name Field


                //         // Updated Stocks Field
                //         // TextInput::make('updated_stocks')
                //         //     ->numeric()
                //         //     ->default(0)
                //         //     ->required()
                //         //     ->columnSpan([
                //         //         'sm' => 2,
                //         //         'md' => 2,
                //         //         'lg' => 2,
                //         //     ]),

                //         // Is Active Field
                //         // Toggle::make('is_active')
                //         //     ->label('Active')
                //         //     ->default(true)
                //         //     ->required()
                //         //     ->columnSpan([
                //         //         'sm' => 2,
                //         //         'md' => 2,
                //         //         'lg' => 2,
                //         //     ]),
                //     ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('name')->searchable()->label('Crop Name'),
                TextColumn::make('original_stocks')->label('Original Stocks'),
                TextColumn::make('updated_stocks')->label('Current Stocks'),
                // Tables\Columns\ToggleColumn::make('is_active')->label('Active')->afterStateUpdated(function ($record, $state) {

                //     if ($state) {
                //         Notification::make()
                //             ->title('Status was activated')
                //             ->success()
                //             ->send();
                //     } else {
                //         Notification::make()
                //             ->title('Status was deactivated')
                //             ->success()
                //             ->send()
                //         ;
                //     }
                // })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalWidth('5xl')
                    ->mutateFormDataUsing(function (array $data): array {

                         $data['distribution_id'] = $this->getRecord()->id;
                         $data['updated_stocks'] = $data['original_stocks'];


                         return $data;
                    })
                    ->disabled(fn() => $this->getDistribution()->is_locked)
                    ,
                // Tables\Actions\AssociateAction::make(),
            ])
            ->actions([


                Tables\Actions\EditAction::make(),


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
