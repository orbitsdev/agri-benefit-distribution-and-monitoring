<?php

namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Beneficiary;
use App\Models\ImportFailure;
use Filament\Actions\StaticAction;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use App\Imports\BeneficiariesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\FilamentForm;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ArtistResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use App\Filament\Barangay\Resources\DistributionResource;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;

class ManageDistributionDistributionItem extends ManageRelatedRecords
{
    use NestedPage;
    use NestedRelationManager;

    protected static string $resource = DistributionResource::class;

    protected static string $relationship = 'distributionItems';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Items & Beneficiaries';

    protected ?string $heading = 'Manage items & Beneficiaries';

    // public static function getNavigationLabel(): string
    // {
    //     return 'Distribution Items';
    // }
    
    


    public function form(Form $form): Form
    {
        return $form
            ->schema(FilamentForm::distributeItems());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item_id')
            ->columns([
                TextColumn::make('item.name')
                    ->searchable()
                    ->label('Item'),

                TextColumn::make('quantity')
                    ->searchable()
                    ->label('Qty'),
                TextColumn::make('original_quantity')
                    ->label('Original Qty')->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('beneficiaries_count')->counts('beneficiaries')->label('Beneficiaries'),

                Tables\Columns\TextColumn::make('is_locked')
    ->label('Lock Status')
    ->formatStateUsing(function ($state) {
        return $state ? 'Locked' : 'Unlocked';
    })
    ->icon(function ($state) {
        return $state ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open';
    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        0 => 'gray',
                        1=> 'success',
                        default=> 'gray'
                        
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('Total Item')
                    ->color('gray')
                    ->label('Items ( ' . $this->getRecord()->distributionItems()->count() . ' ) ')
                    ->link()
                    ->modalSubmitAction(false)

                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                    ->closeModalByClickingAway(false)->modalWidth('7xl')
                    ->hidden(function () {
                        return $this->getRecord()->distributionItems()->count() === 0;
                    }),
                Action::make('Import Failures')
                    ->modalSubmitAction(false)
                    ->action(function () {})
                    ->hidden(function () {
                        return $this->getRecord()->importFailures()->count() === 0;
                    })
                    ->modalContent(fn(): View => view(
                        'livewire.view-import-failure',
                        [
                            'importFailures' => $this->getRecord()->importFailures,
                        ]

                    ))
                    ->outlined()
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                    ->closeModalByClickingAway(false)->modalWidth('7xl'),

                Tables\Actions\CreateAction::make()->label('Add Item'),
            ])
            ->actions([

              
                Action::make('Lock and Unlock')->action(function(Model $record){




                    $record->is_locked = !$record->is_locked;

                    if($record->is_locked){
                        $record->original_quantity = $record->quantity;
                    }

                    $record->save();
                
                   
                    Notification::make()
                        ->title('Lock/Unlock Status')
                        ->success()
                        ->body("Status of distribution '{$record->title}' has been updated to " . ($record->is_locked ? 'Locked' : 'Unlocked') . ".")
                        ->send();
                
                })->requiresConfirmation()
                  ->button()
                  ->outlined(function(Model $record){
                    return !$record->is_locked;
                  })
                  ->icon('heroicon-o-lock-closed')
                  ->color(function(Model $record){
                    return $record->is_locked ? 'danger' : 'primary';
                  })
                  ->label(function(Model $record){
                      return $record->is_locked ? 'Unlock' : 'Lock ';
                  })
                  ->modalDescription(function (Model $record) {
                    return $record->is_locked 
                        ? "Are you sure you want to unlock this item? Unlocking will allow modifications. Be careful with your decision." 
                        : "Are you sure you want to lock this item? Locking will prevent further modifications. Be careful with your decision.";
                })
                  ->tooltip(function(Model $record){
                    return $record->is_locked 
                    ? 'This item is currently locked and cannot be modified. Be careful with your decision. Click to unlock and enable editing.' 
                    : 'This item is currently unlocked. Be careful with your decision. Click to lock and prevent modifications.';
                  })  
                  ->size(ActionSize::ExtraSmall),

                    Action::make('Import')
                    ->size(ActionSize::ExtraSmall)
                    ->button()
                    ->action(function (array $data, Model $record): void {
                        if (!$record->id || !$record->distribution_id) {
                            Notification::make()
                                ->title('Import Failed')
                                ->danger()
                                ->body('The selected distribution item is invalid or does not belong to a valid distribution.')
                                ->send();
                            return;
                        }
                
                        $distributionItemId = $record->id;
                        $distributionId = $record->distribution_id;
                
                        // Get the file path
                        $file = Storage::disk('public')->path($data['file']);
                
                        // Track failure count directly during import
                        $failures = 0;
                
                        try {
                            // Use a custom import class with error tracking
                            Excel::import(new BeneficiariesImport($distributionItemId, $distributionId, $failures), $file);
                
                            // Clean up the uploaded file
                            if (Storage::disk('public')->exists($data['file'])) {
                                Storage::disk('public')->delete($data['file']);
                            }
                
                            // Count total uploaded records
                            $totalUploaded = Beneficiary::where('distribution_item_id', $distributionItemId)->count();
                
                            // Show notifications based on failures
                            if ($failures > 0) {
                                Notification::make()
                                    ->title('Import Completed with Errors')
                                    ->danger()
                                    ->body("Import completed, but $failures rows failed. Total records uploaded: $totalUploaded.")
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Import Successful')
                                    ->success()
                                    ->body("All rows were imported successfully. Total records uploaded: $totalUploaded.")
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Import Failed')
                                ->danger()
                                ->body("An error occurred during import: {$e->getMessage()}")
                                ->send();
                        }
                    })
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        FileUpload::make('file')
                            ->acceptedFileTypes([
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/csv',
                                'text/csv',
                                'text/plain',
                            ])
                            ->disk('public')
                            ->directory('imports')
                            ->label('Excel File'),
                    ])
                    ->outlined()
                    ->button()
                    ->label('Import ')
                    ->modalHeading('Import Beneficiary File')
                    ->modalDescription('Import an Excel file containing beneficiary data. The file should have the column **Name**.')
                    ->hidden(function(Model $record){
                        return $record->is_locked;
                    })
                    ,
                  

                    Action::make('View Beneficiaries') // Disable closing the modal by clicking outside
                    ->modalWidth('7xl')
                    ->size(ActionSize::ExtraSmall) // Set modal width
                    ->button()
                    
                    ->label('Beneficiaries') // Add label for better UX
                    ->icon('heroicon-o-eye') // Optional: Add an icon for better UI
                    ->url(function (Model $record) {
                        return route('filament.barangay.resources.distribution-items.beneficiaries', ['record' => $record->id]);
                    }, shouldOpenInNewTab: true)
                    ->hidden(function (Model $record) {
                        return !$record->hasBeneficiaries();
                    }),


                ActionGroup::make([

                    // Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->label('Manage'),
                    Tables\Actions\DeleteAction::make()->color('gray'),

                ]),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make()
                //     ,
                // ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->byDistribution($this->getRecord()->id);
            })
        ;
    }
}
