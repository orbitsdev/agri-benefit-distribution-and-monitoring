<?php

namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SupportRole;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use App\Http\Controllers\FilamentForm;

use Filament\Forms\Components\Section;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use App\Filament\Barangay\Resources\DistributionResource;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;

class ManageDistributionSupports extends ManageRelatedRecords

{

    // use NestedPage;
    // use NestedRelationManager;
    protected static string $resource = DistributionResource::class;

    protected static string $relationship = 'supports';

    // protected static ?string $navigationIcon = 'herp';
    protected static ?string $navigationIcon = 'fluentui-people-team-24';

    protected ?string $heading = 'Manage Supports';

    public static function getNavigationLabel(): string
    {
        return 'Supports';
    }


    protected function getDistribution(): Model
    {
        return $this->getOwnerRecord();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Support Details')
                    ->description('Specify the personnel and roles involved in the distribution process.')
                    ->columns([
                        'sm' => 2,
                        'md' => 4,
                        'lg' => 6,
                        'xl' => 8,
                        '2xl' => 12,
                    ])
                    ->columnSpanFull()
                    ->schema([
                        // Personnel ID Field
                        Select::make('personnel_id')
                            ->relationship(
                                name: 'personnel',
                                modifyQueryUsing: fn(Builder $query) => $query->byBarangay(Auth::user()->barangay_id)->notRegisteredInSameDistribution($this->getRecord(),)->active()

                            )
                            ->getOptionLabelFromRecordUsing(fn(Model $personnel) => $personnel->user?->name . ' (' . $personnel->user?->email . ')')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Select the assigned personnel.')
                            // ->hidden(fn ($operation) => $operation === 'create')

                            ->columnSpan([
                                'sm' => 2,
                                'md' => 4,
                                'lg' => 4,
                            ])
                            ->hidden(fn($operation) => $operation === 'edit'),

                        // Support Role Field
                        Select::make('type')
                            ->label('Support Role')
                            ->options(SupportRole::byBarangay(Auth::user()->barangay_id)->active()->get()->pluck('name', 'name'))
                            ->searchable()
                            ->helperText('Specify the role (e.g., Scanner, Checker).')
                            ->preload()
                            ->required()
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 4,
                                'lg' => 4,
                            ])->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\Textarea::make('description')
                                    ->required(),

                            ])
                            ->createOptionUsing(function (array $data) {
                                $data['barangay_id'] = Auth::user()->barangay_id;
                              return SupportRole::create($data)->name;

                            }),


                        // Unique Code Field
                        TextInput::make('unique_code')
                            ->label('Code')
                            ->nullable()
                            ->maxLength(191)
                            ->helperText('Enter a unique code for this support role if applicable.')
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 4,
                                'lg' => 4,
                            ])
                            ->hidden(fn($operation) => $operation === 'create')
                            ->disabled(fn($operation) => $operation === 'edit'),

                        // Enable Item Scanning Toggle
                        Toggle::make('enable_item_scanning')
                        ->label('Item Scanning')
                        ->default(false)
                        ->helperText('Allow this person to scan items using QR codes.')
                        ->columnSpan([
                            'sm' => 2,
                            'md' => 4,
                            'lg' => 'full',
                        ]),

                    Toggle::make('enable_beneficiary_management')
                        ->label('Beneficiary Management')
                        ->default(false)
                        ->helperText('Allow this person to manage beneficiary records.')
                        ->columnSpan([
                            'sm' => 2,
                            'md' => 4,
                            'lg' => 'full',
                        ]),


                        // Enable List Access Toggle
                        // Toggle::make('enable_list_access')
                        //     ->label('Enable List Access')
                        //     ->default(false)
                        //     ->helperText('Enable this to access beneficiary lists.')
                        //     ->columnSpan([
                        //         'sm' => 2,
                        //         'md' => 4,
                        //         'lg' => 'full',
                        //     ])->default(true),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('personnel.user.name')
            ->columns([
                Tables\Columns\TextColumn::make('personnel.user.name')->searchable()->label('Personnel'),
                // Tables\Columns\TextColumn::make('personnel.user.email')->searchable(),
                // Tables\Columns\TextColumn::make('personnel.contact_number'),
                Tables\Columns\TextColumn::make('unique_code')->searchable()->label('Code')->tooltip('This will be use for scanning QR code')->copyable(),
                Tables\Columns\TextColumn::make('type')->badge()->color('gray')->label('Support Role'),

                ViewColumn::make('Capabilities')->view('tables.columns.support-capability'),
            ])
            ->filters([


                SelectFilter::make('type')
                    ->label('Support Role')
                    ->options(SupportRole::all()->pluck('name', 'name'))
                    ->searchable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalWidth('7xl')
                    ->disabled(fn() => $this->getDistribution()->is_locked)
                    ->before(function () {
                        if ($this->getDistribution()->is_locked) {
                            Notification::make()
                                ->title('Distribution is locked')
                                ->body('You cannot create support for a locked distribution.')
                                ->danger()
                                ->send();
                        }
                    }),
                // Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                ActionGroup::make([

                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->disabled(fn (Model $record) => $this->getDistribution()->is_locked) // Disable if distribution is locked
                    ->tooltip(function (Model $record) {
                        if ($this->getDistribution()->is_locked) {
                            return 'Cannot edit support for a locked distribution.';
                        }
                        return null;
                    }),
                    Tables\Actions\DeleteAction::make()
                    ->color('gray')
                    ->hidden(fn (Model $record) => $record->hasTransactions(Auth::user()->barangay_id))
                    ->disabled(fn (Model $record) => $this->getDistribution()->is_locked) // Disable if distribution is locked
                    ->tooltip(function (Model $record) {
                        if ($this->getDistribution()->is_locked) {
                            return 'Cannot delete support for a locked distribution.';
                        }
                        return null;
                    }),
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     // Tables\Actions\DissociateBulkAction::make(),
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
