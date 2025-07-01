<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Models\PurchaseOrder;
use App\Models\InventoryItem;
use App\Models\Supplier;
use App\Models\Customer;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Carbon;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationGroup = 'Orders';
    protected static ?string $navigationLabel = 'Purchase Orders';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Purchase Order')
                    ->tabs([
                        Tabs\Tab::make('Order Details')
                            ->schema([
                                Section::make('Order Information')
                                    ->schema([
                                        Select::make('supplier_id')
                                            ->label('Supplier')
                                            ->options(
                                                \App\Models\Supplier::all()
                                                    ->mapWithKeys(fn ($supplier) => [
                                                        $supplier->supplier_id => "Supplier ID - {$supplier->supplier_id} | Name - {$supplier->name}"
                                                    ])
                                                    ->toArray()
                                            )
                                            ->searchable()
                                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $supplier = \App\Models\Supplier::find($state);
                                                $set('supplier_name', $supplier?->name);
                                                $set('supplier_email', $supplier?->email);
                                                $set('supplier_phone', $supplier?->phone_1);
                                            })
                                            ->afterStateHydrated(function ($state, callable $set) {
                                                $supplier = \App\Models\Supplier::find($state);
                                                $set('supplier_name', $supplier?->name);
                                                $set('supplier_email', $supplier?->email);
                                                $set('supplier_phone', $supplier?->phone_1);
                                            }),

                                        TextInput::make('supplier_name')
                                            ->label('Name')
                                            ->disabled(),

                                        TextInput::make('supplier_email')
                                            ->label('Email')
                                            ->disabled(),

                                        TextInput::make('supplier_phone')
                                            ->label('Phone')
                                            ->disabled(),

                                        DatePicker::make('wanted_date')
                                            ->label('Wanted Delivery Date')
                                            ->required()
                                            ->minDate(now()),

                                        Textarea::make('special_note')
                                            ->label('Special Note')
                                            ->nullable()
                                            ->columnSpan(2), 

                                        Hidden::make('user_id')
                                            ->default(fn () => auth()->id()),
                                    ])
                                    ->columns(2)
                                ]),

                        Tabs\Tab::make('Order Items')
                            ->schema([
                                Section::make('Items')
                                    ->schema([
                                        Repeater::make('items')
                                            ->relationship('items')
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        Select::make('inventory_item_id')
                                                            ->label('Item')
                                                            ->relationship('inventoryItem', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->id} | Item Code - {$record->item_code} | Name - {$record->name}")
                                                            ->required(),

                                                        TextInput::make('quantity')
                                                            ->label('Quantity')
                                                            ->numeric()
                                                            ->required()
                                                            ->live()
                                                            ->afterStateUpdated(function ($state, callable $set) {
                                                                $set('remaining_quantity', $state);
                                                                $set('arrived_quantity', 0);
                                                            }),

                                                        TextInput::make('price')
                                                            ->label('Price')
                                                            ->numeric()
                                                            ->required(),

                                                        Hidden::make('remaining_quantity')
                                                            ->default(fn ($get) => $get('quantity')),

                                                        Hidden::make('arrived_quantity')
                                                            ->default(0),
                                                    ]),
                                            ])
                                            ->columns(1)
                                            ->minItems(1)
                                            ->createItemButtonLabel('Add Order Item')
                                            ->columnSpan('full'),
                                    ])
                                    ->columnSpan('full'),
                            ])
                            ->columns(1),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Order ID')
                    ->formatStateUsing(fn ($state) => str_pad($state, 5, '0', STR_PAD_LEFT)),
                TextColumn::make('supplier_id')->label('Supplier ID'),
                TextColumn::make('supplier.name')->label('Supplier Name')->searchable()->sortable(),
                TextColumn::make('wanted_date')->label('Wanted Date')->date(),
                TextColumn::make('status')->label('Status')
                    ->badge()
                    ->colors([
                        'planned' => 'gray',
                        'released' => 'blue',
                        'cancelled' => 'red',
                        'completed' => 'green',
                    ]),
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
            ->actions([
                Action::make('handle')
                    ->label('Handle')
                    ->url(fn ($record) => PurchaseOrderResource::getUrl('handle', ['record' => $record]))
                    ->openUrlInNewTab(false),

                EditAction::make()
                    ->visible(fn ($record) => 
                        auth()->user()->can('edit purchase orders') &&
                        $record->status === 'planned'
                    ),

                DeleteAction::make()
                    ->visible(fn ($record) => 
                        auth()->user()->can('delete purchase orders') &&
                        $record->status === 'planned'
                    ),
                
            ])
            ->defaultSort('wanted_date', 'desc')
            ->recordUrl(null);
    }




    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
            'handle' => Pages\HandlePurchaseOrder::route('/{record}/handle'),
        ];
    }
}