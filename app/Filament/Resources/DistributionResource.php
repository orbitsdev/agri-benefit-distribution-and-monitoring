<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Distribution;
use Filament\Resources\Resource;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\ActionSize;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Guava\FilamentNestedResources\Ancestor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DistributionResource\Pages;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\Filament\Resources\DistributionResource\RelationManagers;
use Illuminate\Contracts\View\View;
class DistributionResource extends Resource
{
    use NestedResource;
    protected static ?string $model = Distribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationLabel = 'Distribution';
    protected static ?string $navigationGroup = 'OPERATIONS';

    protected static ?int $navigationSort = 3;

      public static function getBreadcrumb(): string
    {
        return 'Desritbution';
    }

    //disabled breadcrumb url
    public static function getBreadcrumbUrl(): string
    {
        return '/distribution';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Distribution Details')
                            ->collapsible()
                            ->description('Enter all required distribution information.')
                            ->columns([
                                'sm' => 2,
                                'md' => 4,
                                'lg' => 6,
                                'xl' => 8,
                                '2xl' => 12,
                            ])
                            ->columnSpanFull()
                            ->schema([
                                // Title Field
                                Forms\Components\TextInput::make('title')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(191)
                                    ->columnSpanFull(),

                                // Distribution Date Field
                                Forms\Components\DatePicker::make('distribution_date')
                                    ->label('Distribution Date')
                                    ->required()
                                    ->helperText('Set the date for the distribution.')
                                    ->native(false)
                                    ->default(now()->addDay())
                                    ->closeOnDateSelection()
                                    ->columnSpan([
                                        'sm' => 2,
                                        'md' => 4,
                                        'lg' => 12,
                                    ]),

                                     // Description Field
                                Forms\Components\Textarea::make('description')
                                ->label('Description')
                                ->rows(4)
                                ->maxLength(500)
                                ->columnSpanFull(),

                                // Status Fields (using the toggles from original form)
                                // Forms\Components\Toggle::make('is_disbursed')
                                //     ->label('Is Disbursed')
                                //     ->required()
                                //     ->columnSpanFull(),

                                //     Forms\Components\Toggle::make('is_completed')
                                //     ->label('Is Completed')
                                //     ->required()
                                //     ->columnSpanFull(),

                                // Code Field
                                // Forms\Components\TextInput::make('code')
                                //     ->label('Distribution Code')
                                //     ->helperText('This code is auto-generated and cannot be edited.')
                                //     ->columnSpan([
                                //         'sm' => 2,
                                //         'md' => 4,
                                //         'lg' => 6,
                                //     ])
                                //     ->disabled()
                                //     ->hidden(fn($operation) => $operation === 'create'),




                            ]),
                    ])->columnSpan(['lg' => 3]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('distribution_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_disbursed')->label('Disbursed')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Yes' : 'No')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Yes' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('is_completed')->label('Completed')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Yes' : 'No')->badge()->color(fn (string $state): string => $state ? 'success' : 'gray'),
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
                //
            ])
            ->actions([




                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('View')
                    ->size(ActionSize::ExtraSmall)

                    ->label('View')
                    ->icon('heroicon-s-eye')
                    ->modalSubmitAction(false)
                    // ->button()

                    ->modalContent(fn(Model $record): View => view(
                        'livewire.view-distribution',
                        ['record' => $record],
                    ))
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                    ->closeModalByClickingAway(false)->modalWidth('7xl'),
                    Tables\Actions\EditAction::make()->label('Manage'),
                    Tables\Actions\Action::make('Beneficiaries') // Disable closing the modal by clicking outside
                    ->modalWidth('7xl')

                    ->label('Beneficiaries') // Add label for better UX
                    ->icon('heroicon-s-eye') // Optional: Add an icon for better UI
                    ->url(function (Model $record) {

                      return DistributionResource::getUrl('beneficiaries',['record'=>$record->id]);

                    }, shouldOpenInNewTab: true)
                  ,

                    Tables\Actions\Action::make('disburse')
                        ->label('Manage Disbursement')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-banknotes')
                        ->color('gray')
                        ->visible(fn (Distribution $record): bool => !$record->is_disbursed)
                        ->form([
                            Forms\Components\Toggle::make('is_disbursed')
                                ->label('Mark as Disbursed')
                                ->helperText('Toggle to mark this distribution as disbursed.')
                                ->default(true)
                                ->required(),
                        ])
                        ->action(function (Distribution $record, array $data): void {
                            $record->is_disbursed = $data['is_disbursed'];
                            $record->save();
                        }),
                    Tables\Actions\Action::make('complete')
                        ->label('Manage Completion')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check-circle')
                        ->color('gray')
                        ->visible(fn (Distribution $record): bool => !$record->is_completed)
                        ->form([
                            Forms\Components\Toggle::make('is_completed')
                                ->label('Mark as Completed')
                                ->helperText('Toggle to mark this distribution as completed.')
                                ->default(true)
                                ->required(),
                        ])
                        ->action(function (Distribution $record, array $data): void {
                            $record->is_completed = $data['is_completed'];
                            $record->save();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\BarangayDistributionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistributions::route('/'),
            'create' => Pages\CreateDistribution::route('/create'),
            'edit' => Pages\EditDistribution::route('/{record}/edit'),
            'beneficiaries' => Pages\ListOfBarangayBeneficiaries::route('/{record}/beneficiaries'),
            'crops' => Pages\ManageDistributionCrop::route('/{record}/crops'),
            'barangayDistributions' => Pages\ManageDistributionBarangay::route('/{record}/barangayDistributions'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditDistribution::class,
            Pages\ManageDistributionCrop::class,
            Pages\ManageDistributionBarangay::class,

            // Pages\ManageBe::class,
        ]);
    }

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }


}
