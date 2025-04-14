<?php

namespace App\Filament\Resources\BarangayDistributionResource\RelationManagers;

use Filament\Forms;
use App\Models\Crop;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use App\Models\Beneficiary;
use App\Models\CropsToReceived;
use Filament\Actions\StaticAction;
use App\Models\BarangayDistribution;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Contracts\HasInfolists;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Illuminate\Contracts\View\View;
class BeneficiariesRelationManager extends RelationManager
{
    protected static string $relationship = 'beneficiaries';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('rsbsa_no')
                            ->label('RSBSA Number')
                            ->maxLength(255),

                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('middle_name')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('ext_name')
                                    ->label('Name Extension')
                                    ->maxLength(255),
                            ])->columns(2),

                        Forms\Components\DatePicker::make('birthday')
                        ->date()
                            ->label('Date of Birth'),

                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',

                            ]),
                    ])->columns(1),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('contact_num')
                            ->label('Contact Number')
                            ->required()
                            ->maxLength(15)
                            ->tel()
                    ->prefix('+63')
                    ->mask('9999999999')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Address Information')
                    ->schema([
                        Forms\Components\TextInput::make('farmer_address')
                            ->label('Address')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('farmer_address_mun')
                            ->label('Municipality')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('farmer_address_prv')
                            ->label('Province')
                            ->maxLength(255),
                    ])->columns(1),

                Forms\Components\Section::make('Other Information')
                    ->schema([
                        Forms\Components\TextInput::make('agency')
                            ->label('Agency')
                            ->maxLength(255),

                        Forms\Components\Toggle::make('is_approved')
                            ->label('Approved')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Crops to Receive')
                    ->description('Select the crops that this beneficiary will receive')
                    ->schema([
                        Group::make([
                            Select::make('crop_id')
                            ->label('Crop')
                            ->relationship(
                                'crop',
                                'id',
                                modifyQueryUsing: fn(Builder $query) => $query,
                            )
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name}")
                            ->searchable(['name'])
                            ->preload()

                        ])->relationship('cropsToReceive')

                //         TableRepeater::make('distribution_items_lists')
                // ->columnWidths([
                //     'quantity' => '300px',
                // ])
                // ->relationship('cropsToReceive')
                // ->schema([


                // ])
                // ->withoutHeader()
                // ->columnSpan('full')
                // ->addActionLabel('Add Crop')
                // ->label('Crops To Receive')
                // ->maxItems(10),
                    ])->collapsible(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                // Tables\Columns\TextColumn::make('unique_code')
                // ->label('Code')
                // ,
                ViewColumn::make('cropsToReceive.unique_code')->view('tables.columns.crop-qr')->label('Code'),
                Tables\Columns\TextColumn::make('is_approved')
                    ->label('Approved')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                    ->badge()
                    ->alignCenter()
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                // Always visible columns
                Tables\Columns\TextColumn::make('rsbsa_no')
                    ->label('RSBSA')
                    ->searchable(),

                Tables\Columns\TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                // Toggleable columns
                Tables\Columns\TextColumn::make('contact_num')
                    ->label('Contact')
                    ->formatStateUsing(fn ($state) => $state ? '+63 ' . substr($state, -10) : '-')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('gender')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('birthday')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('farmer_address')
                    ->label('Address')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('farmer_address_mun')
                    ->label('Municipality')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cropsToReceive.crop.name')
                    ->listWithLineBreaks()->badge()->color('primary')->label('Crop'),

                // Tables\Columns\TextColumn::make('farmer_address_prv')
                //     ->label('Province')
                //     ->searchable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                //     ToggleColumn::make('is_approved')->label('Status')->alignCenter()->afterStateUpdated(function ($record, $state) {

                //         if ($state) {
                //             Notification::make()
                //                 ->title('Beneficiary was approved')
                //                 ->success()
                //                 ->send();
                //         } else {
                //             Notification::make()
                //                 ->title('Beneficiary was disapproved')
                //                 ->success()
                //                 ->send()
                //             ;
                //         }
                //     }),

                // Tables\Columns\TextColumn::make('agency')
                //     ->searchable()
                //     ->toggleable(isToggledHiddenByDefault: true),



                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->modalWidth('6xl'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('View Qr')
                    ->color('gray')
                    ->label('View QR Code')
                    ->icon('heroicon-s-eye')

                    ->modalSubmitAction(false)
                    ->modalContent(fn(Model $record): View => view(
                        'livewire.crop-to-receive-qr',
                        ['code' => $record->cropsToReceive->unique_code],
                    ))
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Close'))
                    ->closeModalByClickingAway(false)->modalWidth('7xl'),
                    Tables\Actions\ViewAction::make()->color('gray')->modalWidth('6xl'),
                    Tables\Actions\EditAction::make()->modalWidth('6xl'),
                    Tables\Actions\DeleteAction::make()->color('gray'),
                    Tables\Actions\Action::make('manageApproval')
                        ->label('Manage Approval')
                        ->icon('heroicon-o-check-badge')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Toggle::make('is_approved')
                                ->label('Approve Beneficiary')
                                ->default(function ($record) {
                                    return $record->is_approved;
                                })
                                ->helperText('Toggle to approve or disapprove this beneficiary.')
                        ])
                        ->action(function ($record, array $data) {
                            $record->is_approved = $data['is_approved'];
                            $record->save();

                            $status = $data['is_approved'] ? 'approved' : 'disapproved';
                            Notification::make()
                                ->title("Beneficiary {$status}")
                                ->success()
                                ->send();
                        })
                ])->label('Actions'),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->latest();
            })
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
