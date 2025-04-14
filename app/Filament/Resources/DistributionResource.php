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
use Illuminate\Contracts\View\View;
use Filament\Support\Enums\ActionSize;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Builder;
use Guava\FilamentNestedResources\Ancestor;
use App\Filament\Resources\DistributionResource\Pages;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\Filament\Resources\DistributionResource\RelationManagers;
use Filament\Actions\DeleteAction;

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
                // Status columns first for better visibility
                Tables\Columns\TextColumn::make('is_completed')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Completed' : 'In Progress')
                    ->icon(fn(bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                    ->color(fn(bool $state): string => $state ? 'success' : 'warning')
                    ->sortable(),

                Tables\Columns\TextColumn::make('is_disbursed')
                    ->label('Disbursement')
                    ->badge()
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Disbursed' : 'Not Disbursed')
                    ->icon(fn(bool $state): string => $state ? 'heroicon-o-banknotes' : 'heroicon-o-x-circle')
                    ->color(fn(bool $state): string => $state ? 'success' : 'gray')
                    ->sortable(),

                // Then show basic distribution information
                Tables\Columns\TextColumn::make('title')
                    ->label('Distribution Title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('distribution_date')
                    ->label('Distribution Date')
                    ->date()
                    ->sortable(),

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
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([

                    Tables\Actions\Action::make('View')
                        ->label('View Details')
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                        ->modalSubmitAction(false)
                        ->modalContent(fn(Model $record): View => view(
                            'livewire.view-distribution',
                            ['record' => $record],
                        ))
                        ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                        ->closeModalByClickingAway(false)
                        ->modalWidth('7xl'),
                    Tables\Actions\EditAction::make()
                    ->label('Manage')
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray'),
                    // Primary workflow actions first
                    Tables\Actions\Action::make('disburse')
                        ->label(fn (Distribution $record): string => $record->is_disbursed ? 'Cancel Disbursement' : 'Mark as Disbursed')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Distribution $record): string => $record->is_disbursed ? 'Cancel Disbursement' : 'Confirm Disbursement')
                        ->modalDescription(fn (Distribution $record): string => $record->is_disbursed
                            ? 'Are you sure you want to cancel the disbursement status? This will prevent beneficiaries from claiming benefits.'
                            : 'Are you sure you want to mark this distribution as disbursed? This will allow beneficiaries to claim their benefits.')
                        ->icon('heroicon-o-banknotes')
                        ->color('gray')
                        ->action(function (Distribution $record): void {
                            // Begin transaction to ensure all updates happen or none
                            \Illuminate\Support\Facades\DB::beginTransaction();

                            try {
                                // Update the main distribution
                                $record->is_disbursed = !$record->is_disbursed;
                                $record->save();

                                // Update all associated barangay distributions to match
                                $record->barangayDistributions()->update([
                                    'is_disbursed' => $record->is_disbursed
                                ]);

                                \Illuminate\Support\Facades\DB::commit();

                                $status = $record->is_disbursed ? 'marked as disbursed' : 'disbursement cancelled';
                                $barangayAction = $record->is_disbursed ? 'disbursed' : 'undisbursed';

                                Notification::make()
                                    ->title("Distribution {$status}")
                                    ->body("All barangays have been {$barangayAction} as well.")
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\DB::rollBack();

                                Notification::make()
                                    ->title("Error updating distribution")
                                    ->body("There was a problem updating the distribution status: {$e->getMessage()}")
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Tables\Actions\Action::make('complete')
                        ->label(fn (Distribution $record): string => $record->is_completed ? 'Reopen Distribution' : 'Mark as Completed')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Distribution $record): string => $record->is_completed ? 'Reopen Distribution' : 'Complete Distribution')
                        ->modalDescription(fn (Distribution $record): string => $record->is_completed
                            ? 'Are you sure you want to reopen this distribution? This indicates the distribution process is still ongoing.'
                            : 'Are you sure you want to mark this distribution as completed? This indicates all distribution activities are finished.')
                        ->icon('heroicon-o-check-circle')
                        ->color('gray')
                        ->action(function (Distribution $record): void {
                            $record->is_completed = !$record->is_completed;
                            $record->save();

                            $status = $record->is_completed ? 'marked as completed' : 'reopened';
                            Notification::make()
                                ->title("Distribution {$status}")
                                ->success()
                                ->send();
                        }),

                    // Management and view actions
                    Tables\Actions\Action::make('Beneficiaries')
                        ->label('List Of Beneficiaries')
                        ->icon('heroicon-o-user-group')
                        ->color('gray')
                        ->url(function (Model $record) {
                            return DistributionResource::getUrl('beneficiaries', ['record' => $record->id]);
                        }, shouldOpenInNewTab: true),




                    // Tables\Actions\Action::make('crops')
                    //     ->label('Manage Crops')
                    //     ->icon('heroicon-o-shopping-bag')
                    //     ->color('gray')
                    //     ->url(function (Model $record) {
                    //         return DistributionResource::getUrl('crops', ['record' => $record->id]);
                    //     }, shouldOpenInNewTab: true),




                    Tables\Actions\Action::make('Transaction')
                    ->size(ActionSize::ExtraSmall)
                    ->label('Transaction History')
                    ->icon('heroicon-s-clock')
                    ->url(function (Model $record) {
                        return DistributionResource::getUrl('transaction-history', ['record' => $record->id]);
                    }, shouldOpenInNewTab: true)
                    ->hidden(function (Model $record) {
                        return !$record->transactions()->exists();
                    }),
                        DeleteAction::make()->color('gray')
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
            'transaction-history' => Pages\TransactionHistory::route('/{record}/transaction-history'),
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
