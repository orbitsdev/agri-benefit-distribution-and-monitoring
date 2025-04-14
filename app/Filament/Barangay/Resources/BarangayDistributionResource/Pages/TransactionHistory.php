<?php

namespace App\Filament\Barangay\Resources\BarangayDistributionResource\Pages;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Beneficiary;
use App\Models\Transaction;
use App\Models\BarangayDistribution;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Barangay\Resources\BarangayDistributionResource;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class TransactionHistory extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = BarangayDistributionResource::class;
    protected static string $view = 'filament.resources.barangay-distribution-resource.pages.transaction-history';

    public $record;

    public function mount(BarangayDistribution $record): void {
        $this->record = $record;
    }

    public function getHeading(): string
    {
        return __($this->record->distribution->title . ' - ' . $this->record->barangay->name . ' Transaction History');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Transaction::query()->with('media'))
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('image')
                    ->defaultImageUrl(url('/images/placeholder-image.jpg'))
                    ->label('Captured Image')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Claimed' => 'success',
                        'Unclaimed' => 'gray',
                        default => 'gray',
                    })
                    ->label('Status'),

                Tables\Columns\TextColumn::make('performed_at')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->label('Transaction Date'),

                ColumnGroup::make('Beneficiary Information', [
                    Tables\Columns\TextColumn::make('beneficiary_details.first_name')
                        ->formatStateUsing(function ($state, Model $record) {
                            $firstName = $record->beneficiary_details['first_name'] ?? '';
                            $middleName = $record->beneficiary_details['middle_name'] ?? '';
                            $lastName = $record->beneficiary_details['last_name'] ?? '';
                            $extName = $record->beneficiary_details['ext_name'] ?? '';

                            return trim("$firstName $middleName $lastName $extName");
                        })
                        ->searchable(query: function ($query, $search) {
                            return $query->where(function ($query) use ($search) {
                                $query->whereJsonContains('beneficiary_details->first_name', $search)
                                    ->orWhereJsonContains('beneficiary_details->last_name', $search)
                                    ->orWhereJsonContains('beneficiary_details->middle_name', $search);
                            });
                        })
                        ->label('Name'),

                    Tables\Columns\TextColumn::make('beneficiary_details.rsbsa_no')
                        ->searchable(query: function ($query, $search) {
                            return $query->whereJsonContains('beneficiary_details->rsbsa_no', $search);
                        })
                        ->label('RSBSA No.'),

                    Tables\Columns\TextColumn::make('beneficiary_details.contact_num')
                        ->searchable(query: function ($query, $search) {
                            return $query->whereJsonContains('beneficiary_details->contact_num', $search);
                        })
                        ->label('Contact')
                        ->toggleable(isToggledHiddenByDefault: true),

                    Tables\Columns\TextColumn::make('beneficiary_details.email')
                        ->searchable(query: function ($query, $search) {
                            return $query->whereJsonContains('beneficiary_details->email', $search);
                        })
                        ->label('Email')
                        ->toggleable(isToggledHiddenByDefault: true),

                    Tables\Columns\TextColumn::make('beneficiary_details.farmer_address')
                        ->searchable(query: function ($query, $search) {
                            return $query->whereJsonContains('beneficiary_details->farmer_address', $search);
                        })
                        ->label('Address')
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Crop Details', [
                    Tables\Columns\TextColumn::make('crops_details.crop_name')
                        ->searchable(query: function ($query, $search) {
                            return $query->whereJsonContains('crops_details->crop_name', $search);
                        })
                        ->label('Crop')
                        ->wrap(),

                    Tables\Columns\TextColumn::make('crops_details.unique_code')
                        ->searchable(query: function ($query, $search) {
                            return $query->whereJsonContains('crops_details->unique_code', $search);
                        })
                        ->label('Code')
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Distribution Details', [
                    Tables\Columns\TextColumn::make('distribution_details.title')
                        ->searchable(query: function ($query, $search) {
                            return $query->whereJsonContains('distribution_details->title', $search);
                        })
                        ->label('Distribution')
                        ->toggleable(isToggledHiddenByDefault: false),

                    Tables\Columns\TextColumn::make('distribution_details.distribution_date')
                        ->date('M d, Y')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Date'),
                ]),

                ColumnGroup::make('Recorder Details', [
                    Tables\Columns\TextColumn::make('recorder_details.name')
                        ->searchable(query: function ($query, $search) {
                            return $query->whereJsonContains('recorder_details->name', $search);
                        })
                        ->label('Name')
                        ->toggleable(isToggledHiddenByDefault: false),

                    Tables\Columns\TextColumn::make('recorder_details.role')
                        ->label('Role')
                        ->toggleable(isToggledHiddenByDefault: false),

                    Tables\Columns\TextColumn::make('recorder_details.email')
                        ->searchable(query: function ($query, $search) {
                            return $query->whereJsonContains('recorder_details->email', $search);
                        })
                        ->label('Email')
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->options([
                        'Claimed' => 'Claimed',
                        'Unclaimed' => 'Unclaimed',
                    ])
                    ->searchable()
                    ->label('Status'),
            ], layout: FiltersLayout::AboveContent)
            ->headerActions([])
            ->actions([
                Action::make('View History')
                    ->button()
                    ->icon('heroicon-s-eye')
                    ->color('gray')
                    ->label('View')
                    ->modalSubmitAction(false)
                    ->modalContent(fn (Model $record): View => view(
                        'livewire.transaction-details',
                        ['record' => $record],
                    ))
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                    ->closeModalByClickingAway(false)
                    ->modalWidth('7xl'),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->modifyQueryUsing(function ($query) {
                // Filter transactions for the current barangay distribution
                return $query->where('barangay_distribution_id', $this->record->id);
            });
    }
}
