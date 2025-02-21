<?php

namespace App\Filament\Barangay\Resources\DistributionItemResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Beneficiary;
use App\Models\ImportFailure;
use Filament\Actions\StaticAction;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use App\Imports\BeneficiariesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\FilamentForm;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Model;
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
            ->schema(FilamentForm::beneficiaryForm());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ViewColumn::make('code')->view('tables.columns.beneficiary-qr'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('contact')->searchable(),
                Tables\Columns\TextColumn::make('address')->searchable(),
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
                // $distributionItemId= $this->getOwnerRecord()->id;
                // $distributionId= $this->getOwnerRecord()->distribution_id;
                Action::make('Import')
                    ->button()
                    ->action(function (array $data): void {
                        $record = $this->getOwnerRecord();
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
                    ->label('Import Beneficiaries')
                    ->modalHeading('Import Beneficiary File')
                    ->modalDescription('Import an Excel file containing beneficiary data. The file should have the column **Name**.'),

                Tables\Actions\CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                    $data['distribution_item_id'] = $this->getOwnerRecord()->id;

                    return $data;
                }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('View Qr')
                        ->color('gray')
                        ->label('View QR Code')

                        ->modalSubmitAction(false)
                        ->modalContent(fn(Model $record): View => view(
                            'livewire.beneficiary-qr',
                            ['record' => $record],
                        ))
                        ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                        ->closeModalByClickingAway(false)->modalWidth('7xl'),
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
