<?php

namespace App\Filament\Resources\DistributionResource\Pages;

use Filament\Tables\Table;
use App\Jobs\SendQrMailJob;
use App\Models\Beneficiary;
use App\Models\Distribution;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Concerns\InteractsWithInfolists;

use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;

use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\DistributionResource;
use Filament\Tables\Concerns\InteractsWithTable;

class ListOfBarangayBeneficiaries extends Page  implements HasForms, HasTable, HasInfolists
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithInfolists;
    protected static string $resource = DistributionResource::class;

    protected static string $view = 'filament.resources.distribution-resource.pages.list-of-barangay-beneficiaries';

    public $record;

    public function getHeading(): string
{
    return $this->record->title. ' Beneficiaries';
}

    public function mount(Distribution $record): void
{
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
        ->query(Beneficiary::query())
            ->columns([
                // Tables\Columns\TextColumn::make('unique_code')
                // ->label('Code')
                // ,
                ViewColumn::make('cropsToReceive.unique_code')->view('tables.columns.crop-qr')->label('Code'),
               TextColumn::make('is_approved')
                    ->label('Approved')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                    ->badge()
                    ->alignCenter()
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                // Always visible columns
               TextColumn::make('rsbsa_no')
                    ->label('RSBSA')
                    ->searchable(),

               TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable(),

               TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable(),

               TextColumn::make('email')
                    ->searchable(),

                // Toggleable columns
               TextColumn::make('contact_num')
                    ->label('Contact')
                    ->formatStateUsing(fn ($state) => $state ? '+63 ' . substr($state, -10) : '-')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

               TextColumn::make('gender')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

               TextColumn::make('birthday')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),

               TextColumn::make('farmer_address')
                    ->label('Address')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

               TextColumn::make('farmer_address_mun')
                    ->label('Municipality')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

               TextColumn::make('cropsToReceive.crop.name')
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
                Action::make('SendQr')
                ->label('Send QR to Emails')
                ->icon('heroicon-o-paper-airplane')
                ->button()
                ->outlined()
                ->hidden(function () {
                    // Only show if there are beneficiaries with emails
                    $count = Beneficiary::whereHas('barangayDistribution', function($query) {
                        $query->where('distribution_id', $this->record->id);
                    })->whereNotNull('email')->count();

                    return $count === 0;
                })
                ->requiresConfirmation()
                ->modalHeading('Confirm Sending QR Codes')
                ->modalSubheading('Are you sure you want to send QR codes to all beneficiaries with email addresses?')
                ->modalDescription(function () {
                    $count = Beneficiary::whereHas('barangayDistribution', function($query) {
                        $query->where('distribution_id', $this->record->id);
                    })->whereNotNull('email')->count();

                    return "This will send QR codes to {$count} beneficiaries with valid email addresses.";
                })
                ->action(function (): void {
                    // Get all beneficiaries with email addresses for this distribution
                    $beneficiaries = Beneficiary::whereHas('barangayDistribution', function($query) {
                        $query->where('distribution_id', $this->record->id);
                    })
                    ->whereNotNull('email')
                    ->whereHas('cropsToReceive', function($query) {
                        $query->whereNotNull('unique_code');
                    })
                    ->get();

                    $count = $beneficiaries->count();


                    if ($count > 0) {
                        foreach ($beneficiaries as $beneficiary) {
                            dispatch(new SendQrMailJob($beneficiary));
                        }

                        Notification::make()
                            ->title("Sending QR codes to {$count} beneficiaries")
                            ->body("Email delivery has been queued and will be processed in the background.")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('No emails to send')
                            ->body('No beneficiaries with valid email addresses and QR codes were found.')
                            ->warning()
                            ->send();
                    }
                })
                ->closeModalByClickingAway(false)
                ->modalWidth('md'),
                CreateAction::make()->modalWidth('6xl'),
            ])
            ->actions([
                ActionGroup::make([

                    Action::make('View Qr')
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
                   ViewAction::make()->color('gray')->modalWidth('6xl')->infolist(fn (Infolist $infolist): Infolist => $this->infolist($infolist)),
                   EditAction::make()->modalWidth('6xl')->form([
                    Section::make('Personal Information')
                        ->schema([
                            TextInput::make('rsbsa_no')
                                ->label('RSBSA Number')
                                ->maxLength(255),

                            Grid::make()
                                ->schema([
                                    TextInput::make('first_name')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('middle_name')
                                        ->maxLength(255),

                                    TextInput::make('last_name')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('ext_name')
                                        ->label('Name Extension')
                                        ->maxLength(255),
                                ])->columns(2),

                            DatePicker::make('birthday')
                            ->date()
                                ->label('Date of Birth'),

                            Select::make('gender')
                                ->options([
                                    'male' => 'Male',
                                    'female' => 'Female',

                                ]),
                        ])->columns(1),

                    Section::make('Contact Information')
                        ->schema([
                            TextInput::make('contact_num')
                                ->label('Contact Number')
                                ->required()
                                ->maxLength(15)
                                ->tel()
                        ->prefix('+63')
                        ->mask('9999999999')
                                ->maxLength(255),

                            TextInput::make('email')
                                ->email()
                                ->maxLength(255),
                        ])->columns(2),

                    Section::make('Address Information')
                        ->schema([
                            TextInput::make('farmer_address')
                                ->label('Address')
                                ->maxLength(255),

                            TextInput::make('farmer_address_mun')
                                ->label('Municipality')
                                ->maxLength(255),

                            TextInput::make('farmer_address_prv')
                                ->label('Province')
                                ->maxLength(255),
                        ])->columns(1),

                    Section::make('Other Information')
                        ->schema([
                            TextInput::make('agency')
                                ->label('Agency')
                                ->maxLength(255),

                            Toggle::make('is_approved')
                                ->label('Approved')
                                ->default(true),
                        ])->columns(2),


                ])->label('Beneficiary Information'),
                Action::make('send_qr')
                    ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->requiresConfirmation()
                ->label('Send QR To Email ')
                ->action(function (Model $record) {
                    if (empty($record->email)) {
                        Notification::make()
                            ->title('Error')
                            ->body('The beneficiary does not have an email address.')
                            ->danger()
                            ->send();

                        return;
                    }

                    dispatch(new SendQrMailJob($record));

                    Notification::make()
                        ->title('Success')
                        ->body('QR Code has been sent successfully to ' . $record->email)
                        ->success()
                        ->send();
                }),
                   DeleteAction::make()->color('gray'),
                   Action::make('manageApproval')
                        ->label('Manage Approval')
                        ->icon('heroicon-o-check-badge')
                        ->requiresConfirmation()
                        ->form([
                           Toggle::make('is_approved')
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
                return $query->whereHas('barangayDistribution', function($query){
                    $query->where('distribution_id', $this->record->id);
                })->latest();
            })
            ->bulkActions([
                //BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Personal Information')
                    ->schema([
                        InfolistGrid::make(3)
                            ->schema([
                                TextEntry::make('rsbsa_no')
                                    ->label('RSBSA Number'),

                                TextEntry::make('first_name')
                                    ->label('First Name'),

                                TextEntry::make('middle_name')
                                    ->label('Middle Name'),

                                TextEntry::make('last_name')
                                    ->label('Last Name'),

                                TextEntry::make('ext_name')
                                    ->label('Name Extension'),

                                TextEntry::make('gender')
                                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                                TextEntry::make('birthday')
                                    ->label('Date of Birth')
                                    ->date(),
                            ]),
                    ]),

                InfolistSection::make('Contact Information')
                    ->schema([
                        InfolistGrid::make(2)
                            ->schema([
                                TextEntry::make('contact_num')
                                    ->label('Contact Number')
                                    ->formatStateUsing(fn ($state) => $state ? '+63 ' . substr($state, -10) : '-'),

                                TextEntry::make('email')
                                    ->label('Email Address'),
                            ]),
                    ]),

                InfolistSection::make('Address Information')
                    ->schema([
                        InfolistGrid::make(3)
                            ->schema([
                                TextEntry::make('farmer_address')
                                    ->label('Address'),

                                TextEntry::make('farmer_address_mun')
                                    ->label('Municipality'),

                                TextEntry::make('farmer_address_prv')
                                    ->label('Province'),
                            ]),
                    ]),

                InfolistSection::make('Other Information')
                    ->schema([
                        InfolistGrid::make(2)
                            ->schema([
                                TextEntry::make('agency')
                                    ->label('Agency'),

                                TextEntry::make('is_approved')
                                    ->label('Approval Status')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Approved' : 'Pending')
                                    ->badge()
                                    ->color(fn (string $state): string => $state === 'Approved' ? 'success' : 'gray'),
                            ]),
                    ]),

                InfolistSection::make('Crops to Receive')
                    ->schema([
                        TextEntry::make('cropsToReceive.crop.name')
                            ->label('Assigned Crop'),

                        TextEntry::make('cropsToReceive.is_claimed')
                            ->label('Claim Status')
                            ->formatStateUsing(fn ($state) => $state ? 'Claimed' : 'Not Claimed')
                            ->badge()
                            ->color(fn (string $state): string => $state === 'Claimed' ? 'success' : 'gray'),

                        TextEntry::make('cropsToReceive.date_claimed')
                            ->label('Date Claimed')
                            ->dateTime()
                            ->visible(fn ($record) => $record->cropsToReceive && $record->cropsToReceive->is_claimed),

                        TextEntry::make('cropsToReceive.unique_code')
                            ->label('QR Code')
                            ->formatStateUsing(fn ($state) => $state ?? 'No QR code assigned'),
                    ]),
            ]);
    }
}
