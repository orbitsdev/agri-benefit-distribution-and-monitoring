<?php

namespace App\Filament\Barangay\Resources\DistributionItemResource\Pages;

use Filament\Tables\Table;
use App\Jobs\SendQrMailJob;
use App\Models\Beneficiary;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use App\Imports\BeneficiariesImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\FilamentForm;
use Filament\Forms\Contracts\HasForms;



use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use App\Filament\Barangay\Resources\DistributionItemResource;

class ListOfBenificiaries extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    use InteractsWithRecord;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

    }

    protected static string $resource = DistributionItemResource::class;

    protected static string $view = 'filament.barangay.resources.distribution-item-resource.pages.list-of-benificiaries';

    public function getHeading(): string
{
    $item = $this->record->item->name;
    return __($item.' Benificiaries');
}

public function table(Table $table): Table
    {
        return $table
            ->query(Beneficiary::query())
            ->columns([
                ViewColumn::make('code')->view('tables.columns.beneficiary-qr'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault:true),
                TextColumn::make('contact')->searchable(),
                TextColumn::make('address')->searchable()->wrap(),
                TextColumn::make('status')
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
                Action::make('SendQr')
                ->label('Send QR to All Beneficiaries')
                ->icon('heroicon-o-paper-airplane')
                ->button()
                ->hidden(function () {
                    return !$this->record->beneficiaries()->exists();
                })
                ->outlined()
                ->requiresConfirmation() // Ask for confirmation before sending
                ->modalHeading('Confirm Sending QR Codes')
                ->modalSubheading('Are you sure you want to send QR codes to all beneficiaries of this distribution?')
                ->action(function (): void {
                    $beneficiaries = $this->record->beneficiaries->filter(function ($beneficiary) {
                        return !empty($beneficiary->email);
                });


                    foreach ($beneficiaries as $beneficiary) {
                        dispatch(new SendQrMailJob($beneficiary));
                    }


                    Notification::make()
                        ->title('Emails are being sent')
                        ->success()
                        ->send();
                })
                ->closeModalByClickingAway(false)
                ->modalWidth('md'),


                Action::make('Clear Table')
                ->label('Clear All Beneficiaries')

                ->icon('heroicon-o-trash')
                ->button()
                ->hidden(function () {
                    return !$this->record->beneficiaries()->exists();
                })
                ->outlined()
                ->requiresConfirmation() // Ask for confirmation before clearing
                ->modalHeading('Confirm Table Clear')
                ->modalSubheading('Are you sure you want to delete all beneficiaries? This action cannot be undone.')
                ->action(function (): void {

                    // dd($this->getRecord()->beneficiaries()->delete());
                    $this->record->beneficiaries()->delete();
                })
                ->closeModalByClickingAway(false) // Disable closing by clicking outside
                ->modalWidth('md'),
            // $distributionItemId= $this->record->id;
            // $distributionId= $this->record->distribution_id;
            Action::make('Import')
                ->button()
                ->action(function (array $data): void {
                    $record = $this->record;
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

                CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                    $data['distribution_item_id'] = $this->record->id;

                    return $data;
                })->form(FilamentForm::beneficiaryForm()),
            ])
            ->actions([
                Action::make('Claim')
                ->requiresConfirmation()
                ->button()
                ->action(function (Model $beneficiary) {
                    $beneficiary->update(['status' => Beneficiary::CLAIMED]);

                    // Decrease the quantity in the associated distribution item
                    $distributionItem = $beneficiary->distributionItem;
                    if ($distributionItem->quantity > 0) {
                        $distributionItem->decrement('quantity');
                    }

                    $adminId = Auth::user()->role === 'admin' ? Auth::user()->id : null;

                    // Create a transaction record with JSON snapshots
                    $beneficiary->transactions()->create([
                        'barangay_id' => $distributionItem->distribution->barangay_id,
                        'distribution_id' => $distributionItem->distribution_id,
                        'distribution_item_id' => $distributionItem->id,
                        'beneficiary_id' => $beneficiary->id,
                        'support_id' => $beneficiary->support_id,
                        'admin_id' => $adminId,

                        // JSON Snapshots (Storing full details)
                        'barangay_details' => [
                            'id' => $distributionItem->distribution->barangay_id,
                            'name' => $distributionItem->distribution->barangay->name,
                            'location' => $distributionItem->distribution->barangay->location,
                        ],

                        'distribution_details' => [
                            'id' => $distributionItem->distribution_id,
                            'title' => $distributionItem->distribution->title,
                            'location' => $distributionItem->distribution->location,
                            'date' => $distributionItem->distribution->distribution_date,
                            'code' => $distributionItem->distribution->code,
                        ],

                        'distribution_item_details' => [
                            'id' => $distributionItem->id,
                            'name' => $distributionItem->item->name,
                        ],

                        'beneficiary_details' => [
                            'id' => $beneficiary->id,
                            'name' => $beneficiary->name,
                            'contact' => $beneficiary->contact,
                            'address' => $beneficiary->address,
                            'email' => $beneficiary->email,
                            'code' => $beneficiary->code,
                        ],

                        'support_details' => [
                            'id' => null,
                            'name' => Auth::user()->name, // Ensure no error if support is null
                            'type' => 'Admin',
                            'unique_code' => null,
                        ],

                        // Action Details
                        'action' => 'Claimed',
                        'performed_at' => now(),
                    ]);

                    // Send success notification
                    Notification::make()
                        ->title('Beneficiary Claimed Successfully')
                        ->success()
                        ->send();
                })
                ->hidden(function (Model $beneficiary) {
                    return $beneficiary->status === Beneficiary::CLAIMED;
                })
                ->icon('far-hand-back-fist')
                ->color('success'),
                Action::make('Unclaim')
                ->requiresConfirmation()
                ->button()
                ->action(function (Model $beneficiary) {
                    $beneficiary->update(['status' => Beneficiary::UN_CLAIMED]);

                        // Increase the quantity in the associated distribution item
                        $distributionItem = $beneficiary->distributionItem;
                        if ($distributionItem) {
                            $distributionItem->increment('quantity');
                        }

                        // Find and update the associated transaction instead of deleting it
                        $transaction = $beneficiary->transactions()
                            ->where('action', 'Claimed')
                            ->where('beneficiary_id', $beneficiary->id)
                            ->latest()
                            ->first();

                        if ($transaction) {
                            $transaction->update([
                                'action' => 'Unclaimed',
                                'performed_at' => now(), // Log the unclaim action
                            ]);
                        }

                        Notification::make()
                            ->title('Beneficiary Unclaimed Successfully')
                            ->success()
                            ->send();
                })
                ->hidden(function (Model $beneficiary) {
                    return $beneficiary->status === Beneficiary::UN_CLAIMED;
                })
                ->icon('heroicon-o-x-circle')
                ->color('gray')
                ->label('Revert'),









            ActionGroup::make([
                EditAction::make()->form(FilamentForm::beneficiaryForm()),
                DeleteAction::make()->color('gray'),
            ]),


            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->modifyQueryUsing(function($query){
                return $query->where('distribution_item_id',$this->record->id);
            })
            ;
    }
}
