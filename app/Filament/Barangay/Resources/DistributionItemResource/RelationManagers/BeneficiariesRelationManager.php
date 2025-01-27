<?php

namespace App\Filament\Barangay\Resources\DistributionItemResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Beneficiary;
use App\Models\ImportFailure;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use App\Imports\BeneficiariesImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class BeneficiariesRelationManager extends RelationManager
{
    protected static string $relationship = 'beneficiaries';

// mount
    

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('contact')->searchable(),
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    Beneficiary::CLAIMED => 'success',
                  
                    default => 'gray'
                }),
            ])
            ->filters([
                SelectFilter::make('status')
                ->options(Beneficiary::STATUS_OPTIONS)->searchable()
            ])
            ->headerActions([
                // add action that delete beneficiaries
                Action::make('Clear Table')
                ->label('Clear All Beneficiaries')
                
                ->icon('heroicon-o-trash')
                ->button()  
                ->hidden(function () {
                    return !$this->getOwnerRecord()->beneficiaries()->exists();
                })
                ->outlined()
                ->requiresConfirmation() // Ask for confirmation before clearing
                ->modalHeading('Confirm Table Clear')
                ->modalSubheading('Are you sure you want to delete all beneficiaries? This action cannot be undone.')
                ->action(function (): void {
                   
                    // dd($this->getRecord()->beneficiaries()->delete());
                    $this->getOwnerRecord()->beneficiaries()->delete();
                    
                })
                ->closeModalByClickingAway(false) // Disable closing by clicking outside
                ->modalWidth('md'),

                Action::make('Import')
                ->button()
                ->action(function (array $data): void {
                    $distributionId = $this->getOwnerRecord()->id;

                    // Get the file path
                    $file = Storage::disk('public')->path($data['file']);

                    // Validate the headers
                    $requiredColumns = ['name']; // Minimal required columns
                    $fileHeaders = Excel::toArray(null, $file)[0][0] ?? [];

                    // Normalize the headers to lowercase for case-insensitivity
                    $normalizedHeaders = array_map('strtolower', $fileHeaders);
                    $normalizedRequiredColumns = array_map('strtolower', $requiredColumns);

                    // Check if all minimally required columns exist
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

                    // Proceed with the import
                    Excel::import(new BeneficiariesImport($distributionId), $file);

                    // Delete the file after import
                    if (Storage::disk('public')->exists($data['file'])) {
                        Storage::disk('public')->delete($data['file']);
                    }

                    // Check for import failures
                    $failureCount = ImportFailure::where('distribution_id', $distributionId)->count();

                    // Count total uploaded records
                    $totalUploaded = Beneficiary::where('distribution_item_id', $distributionId)->count();

                    if ($failureCount > 0) {
                        Notification::make()
                            ->title('Import Completed with Errors')
                            ->danger()
                            ->body("Import completed, but $failureCount rows failed. Total records uploaded: $totalUploaded. Please review the error log.")
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Import Successful')
                            ->success()
                            ->body("All rows were imported successfully. Total records uploaded: $totalUploaded.")
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

                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                ActionGroup::make([

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
