<?php

namespace App\Http\Controllers;

use App\Models\User;
use Filament\Forms\Get;
use Illuminate\Http\Request;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class FilamentForm extends Controller
{
    public static function userForm():array{
        return [

            Section::make('User Details')
            ->description('Enter all required user information.')
            ->columns([
                'sm' => 2,
                'md' => 4,
                'lg' => 6,
                'xl' => 8,
                '2xl' => 12,
            ])
            ->columnSpanFull()
            ->schema([

               TextInput::make('name')
                ->required()
                ->maxLength(191)
                ->columnSpan([
                    'sm' => 2,
                    'md' => 4,
                    'lg' => 4,
                ]),

                TextInput::make('email')
                ->required()
                ->unique(ignoreRecord: true)
                ->columnSpan([
                    'sm' => 2,
                    'md' => 4,
                    'lg' => 4,
                ]),

                Select::make('role')
                ->default(User::ADMIN)
                ->required()
                ->options(User::ROLE_OPTIONS)
                ->columnSpan([
                    'sm' => 2,
                    'md' => 4,
                    'lg' => 4,
                ])
                ->searchable()
                ->live()
                ->disabled(fn(string $operation): bool => $operation === 'edit'),

                TextInput::make('password')
                ->password()
                ->revealable()
                ->columnSpan([
                    'sm' => 2,
                    'md' => 4,
                    'lg' => 4,
                ])
                ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                ->dehydrated(fn(?string $state): bool => filled($state))
                ->required(fn(string $operation): bool => $operation === 'create')
                ->label(fn(string $operation) => $operation == 'create' ? 'Password' : 'New Password'),

                Select::make('barangay_id')
                ->relationship('barangay', 'name')
                ->label('Barangay')
                ->required()
                ->searchable()
                ->preload()
                ->helperText('Select the barangay associated with this entry.')
                ->columnSpanFull()
                ->placeholder('Select a Barangay...')
                ->hint('You can search by barangay name.') // Adds a helpful hint below the field
                ->validationAttribute('barangay')
                ->hidden(function (Get $get) {
                    return $get('role') !== User::ADMIN;
                })
                , // Improves error message readability




                SpatieMediaLibraryFileUpload::make('image')
                ->columnSpanFull()
                ->label('Profile')
                ->image()
                ->imageEditor()
                ->required(),
            ]),




        ];
    }

    public static function barangayForm(): array
    {
        return [
            Section::make('Barangay Details')
                ->description('Enter all required barangay information.')
                ->columns([
                    'sm' => 2,
                    'md' => 4,
                    'lg' => 6,
                    'xl' => 8,
                    '2xl' => 12,
                ])
                ->columnSpanFull()
                ->schema([

                    // Barangay Name
                    TextInput::make('name')
                        ->label('Barangay Name')
                        ->required()
                        ->maxLength(191)
                        ->columnSpan([
                            'sm' => 2,
                            'md' => 4,
                            'lg' =>12,
                        ]),

                    // Barangay Location
                    Textarea::make('location')
                        ->label('Location/Venue')
                        ->maxLength(500)
                        ->rows(3)
                        ->columnSpanFull()
                        ->required()
                        ,

                    // Chairman Name
                    TextInput::make('chairman_name')
                        ->label('Chairman Name')
                        // ->required()
                        ->maxLength(191)
                        ->columnSpan([
                            'sm' => 2,
                            'md' => 4,
                            'lg' => 6,
                        ]),

                    // Chairman Contact
                    TextInput::make('chairman_contact')
                        ->label('Chairman Contact')
                        ->tel()
                        // ->required()
                        ->maxLength(20)
                        ->columnSpan([
                            'sm' => 2,
                            'md' => 4,
                            'lg' => 6,
                        ]),

                    // Head Name
                    TextInput::make('head_name')
                        ->label('Head Name')
                        // ->required()
                        ->maxLength(191)
                        ->columnSpan([
                            'sm' => 2,
                            'md' => 4,
                            'lg' => 6,
                        ]),

                    // Head Contact
                    TextInput::make('head_contact')
                        ->label('Head Contact')
                        ->tel()
                        // ->required()
                        ->maxLength(20)
                        ->columnSpan([
                            'sm' => 2,
                            'md' => 4,
                            'lg' => 6,
                        ]),

                        SpatieMediaLibraryFileUpload::make('image')
                        ->columnSpanFull()
                        ->label('Barangay Image')
                        ->image()
                        ->imageEditor()
                       ,
                ]),


        ];
    }

    public static function distributionForm(): array
{
    return [
        Group::make()
        ->schema([
            Section::make('Distribution Details')
            ->collapsible()
            ->description('Enter all required distribution information.')
            ->columns([
                'sm' => 2,
                'md' => 4,
                'lg' => 6,
                'xl' => 8,
                '2xl' => 12,
            ])
            ->columnSpanFull()
            ->schema([

                // Title Field
                TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(191)
                    // ->placeholder('Enter the title of the distribution')
                    // ->helperText('Provide a brief and descriptive title for the distribution.')
                    ->columnSpanFull(),

                // Distribution Date Field
                DatePicker::make('distribution_date')
                    ->label('Distribution Date')
                    ->required()
                    ->helperText('Set the date for the distribution.')
                    ->native(false)
                    ->minDate(now())
                    ->columnSpan([
                        'sm' => 2,
                        'md' => 4,
                        'lg' => 6,
                    ]),

                // Status Field
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Planned' => 'Planned',
                        'Ongoing' => 'Ongoing',
                        'Completed' => 'Completed',
                        'Canceled' => 'Canceled',
                    ])
                    ->default('Planned')
                    ->required()

                    ->columnSpan([
                        'sm' => 2,
                        'md' => 4,
                        'lg' => 6,
                    ]),

                // Location Field
                TextInput::make('location')
                    ->label('Location')
                    ->required()
                    ->maxLength(191)


                    ->columnSpanFull(),

                // Description Field
                Textarea::make('description')
                    ->label('Description')

                    ->rows(4)
                    ->maxLength(500)
                    // ->helperText('Add more details about the distribution if needed.')
                    ->columnSpanFull(),

                // Code Field (Read-only, Auto-generated)
                TextInput::make('code')
                    ->label('Distribution Code')
                    ->helperText('This code is auto-generated and cannot be edited.')
                    ->columnSpan([
                        'sm' => 2,
                        'md' => 4,
                        'lg' => 6,
                    ])
                    ->disabled()
                    ->hidden(fn($operation) => $operation === 'create'),
            ]),
        ])->columnSpan(['lg' => 3]),
        // Group::make()
        // ->schema([
        //     Section::make('To Distribute Item')
        //     ->description('Manage what Item To Distribute')
        //     ->columns([
        //         'sm' => 2,
        //         'md' => 4,
        //         'lg' => 6,
        //         'xl' => 8,
        //         '2xl' => 12,
        //     ])  ->columnSpanFull()
        //     ->schema([
        //         ...self::distibuteItemForm()
        //     ])
        // ])->columnSpan(['lg' => 3]),


    ];
}

public static function distibuteItemForm(): array {
    return [
        TableRepeater::make('distribution_items_lists')
                                ->columnWidths([
                                    'quantity' => '300px',
                                ])
                                ->relationship('distributionItems')
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
                                ])
                                ->withoutHeader()
                                ->columnSpan('full')
                                ->addActionLabel('Add Item')
                                ->label('Items')
                                ->maxItems(10),
    ];
}
public static function distributeItems(): array
{
    return [
        Section::make('Distribution Details')
            ->collapsible()
            ->description('Provide all necessary details about the distribution, including the item and quantity to distribute.')
            ->columns([
                'sm' => 2,
                'md' => 4,
                'lg' => 6,
                'xl' => 8,
                '2xl' => 12,
            ])
            ->columnSpanFull()
            ->schema([
                Select::make('item_id')
                    ->label('Item')
                    ->relationship(
                        'item',
                        'id',
                        modifyQueryUsing: fn (Builder $query) => $query
                    )
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name}")
                    ->searchable(['name'])
                    ->preload()
                    ->required()
                    ->columnSpan([
                        'sm' => 2,
                        'md' => 4,
                        'lg' => 6,
                    ]),

                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->columnSpan([
                        'sm' => 2,
                        'md' => 4,
                        'lg' => 6,
                    ]),
            ]),
    ];
}



}
