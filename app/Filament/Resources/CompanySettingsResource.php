<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanySettingsResource\Pages;
use App\Models\Company;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Fieldset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;
use App\Helpers\Countries; 
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\FileUpload;


class CompanySettingsResource extends Resource
{
    protected static ?string $model = Company::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Company Settings';
    protected static ?string $modelLabel = 'System';
    protected static ?string $slug = 'company-settings';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('updated_by')
                    ->default(Auth::id())
                    ->disabled(),
                    
                Tabs::make('Settings')
                    ->tabs([
                        // Company Details Tab
                        Tabs\Tab::make('Company Details')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Section::make('Basic Information')
                                    ->description('Enter your company registration details')
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->columnSpanFull(),
                                        
                                        Fieldset::make('Address')
                                            ->schema([
                                                TextInput::make('address_line_1')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('address_line_2')
                                                    ->maxLength(255),
                                                TextInput::make('address_line_3')
                                                    ->maxLength(255),
                                                TextInput::make('city')
                                                    ->required()
                                                    ->maxLength(100),
                                                TextInput::make('postal_code')
                                                    ->required()
                                                    ->maxLength(20),
                                                Select::make('country')
                                                    ->options(Countries::all())
                                                    ->default('Sri Lanka')
                                                    ->required()
                                                    ->searchable()
                                                    ->native(false),
                                            ])->columns(2),
                                        
                                        Fieldset::make('Contact Information')
                                            ->schema([
                                                TextInput::make('primary_phone')
                                                    ->tel()
                                                    ->required()
                                                    ->maxLength(20),
                                                TextInput::make('secondary_phone')
                                                    ->tel()
                                                    ->maxLength(20),
                                                TextInput::make('email')
                                                    ->required()
                                            ])->columns(2),
                                        
                                        DatePicker::make('started_date')
                                            ->required()
                                            ->displayFormat('Y-m-d')
                                            ->native(false),
                                        
                                        FileUpload::make('logo')
                                            ->label('Company Logo')
                                            ->image()
                                            ->directory('company-logos')
                                            ->maxSize(2048) 
                                            ->imagePreviewHeight('150') 
                                            ->nullable()
                                            ->columnSpanFull(),
                                            
                                        Textarea::make('special_notes')
                                            ->columnSpanFull()
                                            ->maxLength(500),
                                    ]),
                            ]),
                            
                        // Owner Details Tab
                        Tabs\Tab::make('Owner Details')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                Section::make('Owner Information')
                                    ->description('Enter the primary owner details')
                                    ->schema([
                                        TextInput::make('owner.name')
                                            ->required()
                                            ->maxLength(255),
                                            
                                        Fieldset::make('Owner Address')
                                            ->schema([
                                                TextInput::make('owner.address_line_1')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('owner.address_line_2')
                                                    ->maxLength(255),
                                                TextInput::make('owner.address_line_3')
                                                    ->maxLength(255),
                                                TextInput::make('owner.city')
                                                    ->required()
                                                    ->maxLength(100),
                                                TextInput::make('owner.postal_code')
                                                    ->required()
                                                    ->maxLength(20),
                                                Select::make('owner.country')
                                                    ->options(Countries::all())
                                                    ->default('Sri Lanka')
                                                    ->required()
                                                    ->searchable()
                                                    ->native(false),
                                            ])->columns(2),
                                        
                                        Fieldset::make('Owner Contact')
                                            ->schema([
                                                TextInput::make('owner.phone_1')
                                                    ->tel()
                                                    ->required()
                                                    ->maxLength(20),
                                                TextInput::make('owner.phone_2')
                                                    ->tel()
                                                    ->maxLength(20),
                                                TextInput::make('owner.email')
                                                    ->email()
                                                    ->maxLength(255),
                                            ])->columns(2),
                                        
                                        DatePicker::make('owner.joined_date')
                                            ->required()
                                            ->displayFormat('Y-m-d')
                                            ->native(false),
                                            
                                        Hidden::make('owner.updated_by')
                                            ->default(Auth::id()),
                                    ]),
                            ]),
                            
                        // Management Tab
                        Tabs\Tab::make('Management')
                            ->icon('heroicon-o-users')
                            ->schema([
                                Section::make('Management Team')
                                    ->description('Configure your company management structure')
                                    ->schema([
                                        Repeater::make('management')
                                            ->relationship()
                                            ->schema([
                                                Hidden::make('id'),
                                                
                                                Select::make('user_id')
                                                    ->label('Employee')
                                                    ->options(
                                                        User::whereDoesntHave('roles', function($q) {
                                                            $q->where('name', 'admin'); 
                                                        })
                                                        ->pluck('name', 'id')
                                                    )
                                                    ->required()
                                                    ->searchable()
                                                    ->native(false)
                                                    ->columnSpan(1),
                                                    
                                                Select::make('position')
                                                    ->options([
                                                        'CEO' => 'Chief Executive Officer',
                                                        'GM' => 'General Manager',
                                                        'Finance Manager' => 'Finance Manager',
                                                        'QC' => 'Quality Controller',
                                                        'Technician' => 'Technician',
                                                        'Cutting Supervisor' => 'Cutting Supervisor',
                                                        'Sewing Line Supervisor' => 'Sewing Line Supervisor',
                                                    ])
                                                    ->required()
                                                    ->columnSpan(1),
                                                    
                                                DatePicker::make('appointed_date')
                                                    ->required()
                                                    ->displayFormat('Y-m-d')
                                                    ->native(false)
                                                    ->columnSpan(1),
                                                    
                                                Hidden::make('updated_by')
                                                    ->default(auth()->id()),
                                            ])
                                            ->columns(3)
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string => 
                                                isset($state['user_id']) 
                                                    ? User::find($state['user_id'])?->name . ' - ' . ($state['position'] ?? '')
                                                    : null
                                            )
                                            ->reorderable()
                                            ->addActionLabel('Add Management Member')
                                            ->minItems(0)
                                            ->maxItems(20),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('primary_phone')
                    ->searchable()
                    ->sortable()
                    ->label('Phone'),
                    
                Tables\Columns\TextColumn::make('started_date')
                    ->date('Y-m-d')
                    ->sortable()
                    ->label('Established'),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit Settings'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Remove delete bulk action since we can't create new records
                ]),
            ])
            ->emptyStateActions([
                // No create action needed since canCreate() returns false
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add relations if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanySettings::route('/'),
            'edit' => Pages\EditCompanySettings::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->hasRole('admin');
    }

}