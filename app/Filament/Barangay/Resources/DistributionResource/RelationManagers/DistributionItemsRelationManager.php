<?php

namespace App\Filament\Barangay\Resources\DistributionResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class DistributionItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'distributionItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('item_id')
                                        ->label('Item')
                                        ->relationship(
                                            'item',
                                            'id',
                                            modifyQueryUsing: fn(Builder $query) => $query,
                                        )
                                        ->distinct()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name}")
                                        ->searchable(['name'])
                                        ->preload()
                                        ->required(),
                                    TextInput::make('quantity')
                                        ->numeric()
                                        
                                        ->minValue(1)
                                        ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item_id')
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->searchable()
                    ->label('Item'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                
            ])
            ->actions([
                Action::make('Import')
    ->button()
    ->action(function (array $data): void {
        // Uncomment and modify the following lines as needed for import functionality:

        // $file  = Storage::disk('public')->path($data['file']);
        // Excel::import(new BeneficiariesImport, $file);

        // if (Storage::disk('public')->exists($data['file'])) {
        //     Storage::disk('public')->delete($data['file']);
        // }
    })
    ->icon('heroicon-o-arrow-up-tray')
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
    ->modalHeading('Upload Beneficiary File')
    ->modalDescription('Follow these instructions to import beneficiaries into the system:

1. Ensure your file is in the correct format (`.xlsx`, `.xls`, or `.csv`).
2. The file must include these columns:
   - **First Name**: The beneficiary\'s first name.
   - **Middle Name**: The beneficiary\'s middle name (optional).
   - **Last Name**: The beneficiary\'s last name.
   - **Unique Beneficiary ID**: This must be unique for each beneficiary.

3. If updating existing beneficiaries, ensure the "Unique Beneficiary ID" matches records in the system. Otherwise, new beneficiaries will be added.
4. Verify your data before uploading to prevent errors.

Thank you for your cooperation!'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
