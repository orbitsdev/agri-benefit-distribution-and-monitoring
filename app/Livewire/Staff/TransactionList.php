<?php

namespace App\Livewire\Staff;

use Filament\Tables;
use Livewire\Component;
use App\Models\Transaction;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use Filament\Actions\StaticAction;
use WireUi\Traits\WireUiActions;
use Illuminate\Contracts\View\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class TransactionList extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use WireUiActions;

    public $distribution;

    public function mount($distribution = null)
    {
        $this->distribution = $distribution;
    }
    
    #[On('beneficiary-claimed')]
    #[On('beneficiary-unclaimed')]
    public function refreshTransactions($distribution = null)
    {
        // If we receive a specific distribution ID and it matches our current one, refresh the table
        if ($distribution === null || $distribution == $this->distribution) {
            $this->resetTable();
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
                    ->with('media')
                    ->when($this->distribution, function ($query) {
                        return $query->where('barangay_distribution_id', $this->distribution);
                    })
                    ->latest()
            )
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('image')
                    ->defaultImageUrl(url('/images/placeholder-image.jpg'))
                    ->label('Captured Image')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'claim' => 'success',
                        'revert' => 'danger',
                        default => 'gray',
                    })
                    ->label('Status'),

                Tables\Columns\TextColumn::make('created_at')
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
                ]),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->options([
                        'claim' => 'Claimed',
                        'revert' => 'Unclaimed',
                    ])
                    ->searchable()
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_details')
                    ->button()
                    ->icon('heroicon-s-eye')
                    ->color('gray')
                    ->label('View')
                    ->modalSubmitAction(false)
                    ->modalContent(fn (Model $record): View => view(
                        'livewire.barangay-transaction-details',
                        ['record' => $record],
                    ))
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                    ->closeModalByClickingAway(false)
                    ->modalWidth('7xl'),
            ])
            ->striped()
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('No Transactions Found')
            ->emptyStateDescription('There are no transactions recorded for this distribution yet.')
            ->defaultSort('created_at', 'desc');
    }

    public function render()
    {
        return view('livewire.staff.transaction-list');
    }
}
