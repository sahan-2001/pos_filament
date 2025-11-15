<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Customer Management';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('shop_name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone_1')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone_2')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Shipping Information')
                    ->schema([
                        Forms\Components\TextInput::make('shipping_address_line_1')
                            ->label('Address Line 1')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('shipping_address_line_2')
                            ->label('Address Line 2')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('shipping_city')
                            ->label('City')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('shipping_state')
                            ->label('State')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('shipping_zip_code')
                            ->label('ZIP Code')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\Select::make('shipping_country')
                            ->label('Country')
                            ->options(\App\Helpers\Countries::all())
                            ->searchable()
                            ->default('Sri Lanka')
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Billing Information')
                    ->schema([
                        Forms\Components\TextInput::make('billing_address_line_1')
                            ->label('Address Line 1')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('billing_address_line_2')
                            ->label('Address Line 2')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('billing_city')
                            ->label('City')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('billing_state')
                            ->label('State')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('billing_zip_code')
                            ->label('ZIP Code')
                            ->required()
                            ->maxLength(50),

                        Forms\Components\Select::make('billing_country')
                            ->label('Country')
                            ->options(\App\Helpers\Countries::all())
                            ->searchable()
                            ->default('Sri Lanka')
                            ->native(false),
                        ])
                    ->columns(2),

                Forms\Components\Hidden::make('remaining_balance')
                    ->default(0),

                Forms\Components\Hidden::make('requested_by')
                    ->default(fn () => auth()->user()->id),

                Forms\Components\Hidden::make('approved_by'),
            ]);
    }


    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_id')
                    ->label('Customer ID')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => str_pad($state, 5, '0', STR_PAD_LEFT)),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('shop_name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('address')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('phone_1')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('phone_2')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('remaining_balance')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('requestedBy.email')->label('Requested By Email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('approvedBy.email')->label('Approved By Email')->sortable()->searchable(),
                ...(
                Auth::user()->can('view audit columns')
                    ? [
                        TextColumn::make('created_by')->label('Created By')->toggleable()->sortable(),
                        TextColumn::make('updated_by')->label('Updated By')->toggleable()->sortable(),
                        TextColumn::make('created_at')->label('Created At')->toggleable()->dateTime()->sortable(),
                        TextColumn::make('updated_at')->label('Updated At')->toggleable()->dateTime()->sortable(),
                    ]
                    : []
                    ),
            ])
            ->filters([
                // Define your filters if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Customer $record) => auth()->user()->can('edit customers')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Customer $record) => auth()->user()->can('delete customers')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()->can('delete customers')),
            ])
            ->recordUrl(null);
    }

    public static function getRelations(): array
    {
        return [
            // Define any related models or relations
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}