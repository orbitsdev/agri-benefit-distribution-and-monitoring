<?php

namespace App\Filament\Barangay\Resources\DistributionItemResource\Pages;

use Filament\Tables\Table;
use App\Models\Beneficiary;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;

 

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
                TextColumn::make('name')->searchable(),
                TextColumn::make('contact')->searchable(),
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
                CreateAction::make(),
            ])
            ->actions([
                // add action claim and unclaim hide uncclamed if status is claim and hide claim if status is unclaimed add icon as well and add required conmfirmation
                Action::make('Claim')
                ->requiresConfirmation()
                ->button()
                ->action(function(Model $benificiary){
                    $benificiary->update(['status'=>Beneficiary::CLAIMED]);
                   
                })->hidden(function(Model $benificiary){
                    return $benificiary->status === Beneficiary::CLAIMED;
                })->icon('heroicon-o-check-circle')->color('success'),

                Action::make('Unclaim')
                ->requiresConfirmation()
                ->button()
                ->action(function(Model $benificiary){
                    $benificiary->update(['status'=>Beneficiary::UN_CLAIMED]);
                   
                })->hidden(function(Model $benificiary){
                    return $benificiary->status === Beneficiary::UN_CLAIMED;
                })->icon('heroicon-o-x-circle')->color('danger'),
               


               




            ActionGroup::make([
                EditAction::make(),
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
            ]);
    }
}
