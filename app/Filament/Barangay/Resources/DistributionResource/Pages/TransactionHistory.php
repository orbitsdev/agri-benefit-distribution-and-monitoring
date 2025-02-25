<?php

namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use App\Models\Item;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Beneficiary;
use App\Models\Transaction;
use App\Models\Distribution;
use App\Models\DistributionItem;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Barangay\Resources\DistributionResource;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class TransactionHistory extends Page  implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = DistributionResource::class;

    protected static string $view = 'filament.barangay.resources.distribution-resource.pages.transaction-history';

    public $record;
    public function mount(Distribution $record): void {
        // $transaction = \App\Models\Transaction::latest()->with('media')->first();
        // dd($transaction->getMedia()->pluck('collection_name'),$transaction);

    }

    public function getHeading(): string
    {
        $item = $this->record->title;
        return __($item . ' Transaction History');
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(Transaction::query()->with('media'))
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                ->collection('image')
                ->defaultImageUrl(url('/images/placeholder-image.jpg'))->label('Captured Image')
                 ->toggleable(isToggledHiddenByDefault: false)
                ,
                 Tables\Columns\TextColumn::make('action')->badge()
                ->color(fn (string $state): string => match ($state) {

                    'Claimed' => 'success',
                    'Unclaimed' => 'gray',
                })->label('Status')
                ,

                ColumnGroup::make('Beneficiary Information', [
                    Tables\Columns\TextColumn::make('beneficiary_details.name')
                    ->label('Name')
                    ->searchable(isIndividual:true)
                   ,
                    Tables\Columns\TextColumn::make('beneficiary_details.code')
                    ->label('Code')
                    ->searchable(isIndividual:true)
                   ,

                    Tables\Columns\TextColumn::make('beneficiary_details.contact')->searchable(isIndividual:true)->label('Contact')->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('beneficiary_details.email')->searchable(isIndividual:true)->label('Email')->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('beneficiary_details.address')->label('Address')->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Item Details', [
                    Tables\Columns\TextColumn::make('distribution_item_details.name')->searchable()->toggleable(isToggledHiddenByDefault: false)->label('Item')->wrap(),
                    // Tables\Columns\TextColumn::make('distribution_item_name')->searchable()->label('Item')->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Distribution Details', [
                    Tables\Columns\TextColumn::make('distribution_details.title')->searchable()->toggleable(isToggledHiddenByDefault: false)->label('Distribution'),
                    Tables\Columns\TextColumn::make('distribution_details.location')->toggleable(isToggledHiddenByDefault: true)->label('Location'),
                    Tables\Columns\TextColumn::make('distribution_details.date')
                    ->dateTime('M d, Y')
                    ->toggleable(isToggledHiddenByDefault: true)->label('Date'),
                    Tables\Columns\TextColumn::make('distribution_details.code')->searchable(isIndividual:true)->toggleable(isToggledHiddenByDefault: true)->label('Code'),
                    // Tables\Columns\TextColumn::make('distribution_item_name')->searchable()->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Barangay Details ', [
                    // Tables\Columns\TextColumn::make('barangay.name')->searchable(),
                    Tables\Columns\TextColumn::make('barangay_details.name')->searchable(isIndividual:true)->label('Barangay')->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('barangay_details.location')->searchable()->label('Location')->toggleable(isToggledHiddenByDefault: true),
                    // Tables\Columns\TextColumn::make('barangay_location')->searchable()->label('Barangay Location')->toggleable(isToggledHiddenByDefault: true),
                ]),

                // ColumnGroup::make('Recorder Details', [
                //     // Tables\Columns\TextColumn::make('support.personnel.user.name')->searchable(),
                //     Tables\Columns\TextColumn::make('support_details.name')->searchable(isIndividual:true)->label('Name')->toggleable(isToggledHiddenByDefault: false),
                //     Tables\Columns\TextColumn::make('support_details.type')->label('Type')->toggleable(isToggledHiddenByDefault: false),
                //     Tables\Columns\TextColumn::make('support_details.unique_code')->searchable(isIndividual:true)->label('Support Code')->toggleable(isToggledHiddenByDefault: true),
                // ]),

                ColumnGroup::make('Recorded Details', [
                    Tables\Columns\TextColumn::make('recorder_details.name')->searchable(isIndividual:true)->label('Name')->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('recorder_details.role')->label('Role')->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('recorder_details.unique_code')->searchable(isIndividual:true)->label('Support Code')->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('recorder_details.enable_item_scanning')->searchable(isIndividual:true)->label('Can Scan')->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('recorder_details.enable_beneficiary_management')->searchable(isIndividual:true)->label('Can Manage')->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('performed_at')
                    ->dateTime('M d, Y h:i A') // Format: Jan 21, 2025 10:30 AM
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                    // Tables\Columns\TextColumn::make('created_at')
                    //     ->dateTime()
                    //     ->sortable()
                    //     ->toggleable(isToggledHiddenByDefault: true),
                    // Tables\Columns\TextColumn::make('updated_at')
                    //     ->dateTime()
                    //     ->sortable()
                    //     ->toggleable(isToggledHiddenByDefault: true),
                ]),

            ])
            ->filters([

                // SelectFilter::make('distribution_id')
                // ->relationship('distribution', 'title')
                // ->searchable()
                // // ->multiple()
                // ->preload()->label('Distribution'),

                SelectFilter::make('distribution_item_id')
                ->searchable()
                ->getSearchResultsUsing(fn (string $search): array =>
                    DistributionItem::whereHas('item', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    })->where('distribution_id',$this->record->id)

                    ->limit(50)
                    ->get()
                    ->mapWithKeys(fn ($distributionItem) => [$distributionItem->id => $distributionItem->item->name])
                    ->toArray()
                )
                ->getOptionLabelUsing(fn ($value): ?string =>
                    DistributionItem::find($value)?->item->name
                )
                ->multiple()
                ->preload()
                ->label('Item'),



                SelectFilter::make('action')
                ->options(Beneficiary::STATUS_OPTIONS)->searchable()

            ], layout: FiltersLayout::AboveContent)
            ->headerActions([])
            ->actions([
                Action::make('View History')
                ->button()
                ->icon('heroicon-s-eye')
                ->color('gray')
                ->label('View ')
                ->modalSubmitAction(false)
                ->modalContent(fn (Model $record): View => view(
                    'livewire.transaction-details', // ✅ Use Livewire View
                    ['record' => $record],
                ))
                ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                ->closeModalByClickingAway(false)
                ->modalWidth('7xl'),
                ActionGroup::make([

                    Tables\Actions\DeleteAction::make()->color('gray'),
                ]),

            ],position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->modifyQueryUsing(function ($query) {
                return $query->byBarangay(Auth::user()->barangay_id)->latest();
            })
        ;
    }

}
