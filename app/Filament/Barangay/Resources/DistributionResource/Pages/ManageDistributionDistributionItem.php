<?php

namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ImportFailure;
use Filament\Actions\StaticAction;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use App\Imports\BeneficiariesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\FilamentForm;
use Filament\Tables\Columns\TextColumn;
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

    protected static ?string $navigationLabel = 'Manage Items & Beneficiaries';

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
                    ->label('Quantity'),
                    TextColumn::make('beneficiaries_count')->counts('beneficiaries')->label('Beneficiaries'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('Import Failures')
                    ->modalSubmitAction(false)
                    ->action(function () {
                       
                    })
                    ->hidden(function(){
                        return $this->getRecord()->importFailures()->count() === 0;
                    })
                    ->modalContent(fn (): View => view(
                        'livewire.view-import-failure',
                        [
                            'importFailures' => $this->getRecord()->importFailures,
                        ]
                        
                    ))
                    ->outlined()
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                    ->closeModalByClickingAway(false)->modalWidth('7xl'),
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([


                Action::make('Import')
                    ->button()
                    ->action(function (array $data): void {
                        $distributionId = $this->getRecord()->id;

                        // Get the file path
                        $file = Storage::disk('public')->path($data['file']);

                        // Validate the headers
                        $requiredColumns = ['name'];
                        $fileHeaders = \Maatwebsite\Excel\Facades\Excel::toArray(null, $file)[0][0] ?? [];

                        // Normalize the headers to lowercase for case-insensitivity
                        $normalizedHeaders = array_map('strtolower', $fileHeaders);
                        $normalizedRequiredColumns = array_map('strtolower', $requiredColumns);

                        // Check if all required columns exist
                        foreach ($normalizedRequiredColumns as $column) {
                            if (!in_array($column, $normalizedHeaders)) {
                                Notification::make()
                                    ->title('Import Failed')
                                    ->danger()
                                    ->body("The uploaded file is missing the required column: '$column'.")
                                    ->send();

                                // Delete the file and abort the action
                                if (Storage::disk('public')->exists($data['file'])) {
                                    Storage::disk('public')->delete($data['file']);
                                }
                                return;
                            }
                        }

                        // If validation passes, proceed with the import
                        \Maatwebsite\Excel\Facades\Excel::import(new BeneficiariesImport($distributionId), $file);

                        // Delete the file after import
                        if (Storage::disk('public')->exists($data['file'])) {
                            Storage::disk('public')->delete($data['file']);
                        }

                        // Check for import failures
                        $failureCount = ImportFailure::where('distribution_id', $distributionId)->count();

                        if ($failureCount > 0) {
                            Notification::make()
                                ->title('Import Completed with Errors')
                                ->danger()
                                ->body("Import completed, but $failureCount rows failed. Please review the error log.")
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Import Successful')
                                ->success()
                                ->body('All rows were imported successfully.')
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
                    ->label('Upload')
                    ->modalHeading('Upload Beneficiary File')
                    ->modalDescription('Upload an Excel file containing beneficiary data. The file should have the column **Name**.'),


                ActionGroup::make([

                    // Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->color('gray'),

                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
