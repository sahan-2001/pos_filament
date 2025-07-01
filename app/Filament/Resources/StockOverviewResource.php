<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockOverviewResource\Pages;
use App\Models\Stock;
use App\Models\EmergencyStock;
use App\Models\InventoryItem;
use App\Models\Warehouse;
use App\Models\InventoryLocation;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class StockOverviewResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationLabel = 'Stocks';
    protected static ?string $navigationGroup = 'Inventory Management'; 


    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User & Timestamps')
                    ->schema([
                        Forms\Components\TextInput::make('user_id')
                            ->label('User ID')
                            ->default(fn () => auth()->id())
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('user_email')
                            ->label('User Email')
                            ->default(fn () => auth()->user()?->email)
                            ->disabled(),

                        Forms\Components\DatePicker::make('received_date')
                            ->label('Received Date')
                            ->default(now())
                            ->dehydrated()
                            ->required(),

                        Forms\Components\DatePicker::make('updated_date')
                            ->label('Updated Date')
                            ->default(today())
                            ->dehydrated()
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Stock Details')
                    ->schema([
                        Forms\Components\Select::make('item_id')
                            ->label('Item')
                            ->options(
                                \App\Models\InventoryItem::whereNull('deleted_at')
                                    ->get()
                                    ->mapWithKeys(fn ($item) => [
                                        $item->id => "{$item->item_code} - {$item->name}"
                                    ])
                            )
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->dehydrated()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('available_quantity', null);
                                $set('quantity', null);
                                $set('location_id', null);

                                if ($state) {
                                    $set('available_quantity', \App\Models\InventoryItem::find($state)?->available_quantity);
                                }
                            }),


                        Forms\Components\TextInput::make('available_quantity')
                            ->label('Available Quantity')
                            ->disabled()
                            ->dehydrated(false), 

                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minValue(1)
                            ->dehydrated()
                            ->required(),
                        
                        Forms\Components\TextInput::make('cost')
                            ->label('Cost')
                            ->numeric()
                            ->dehydrated()
                            ->required(),

                        Forms\Components\Select::make('location_id')
                            ->label('Location (Picking)')
                            ->options(
                                \App\Models\InventoryLocation::where('location_type', 'picking')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->dehydrated()
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->query(Stock::query()->where('quantity', '>', 0)) 
            ->columns([
                TextColumn::make('location.warehouse.name')
                    ->label('Warehouse')
                    ->sortable(),

                TextColumn::make('location.name')
                    ->label('Location')
                    ->sortable(),

                TextColumn::make('item.item_code')
                    ->label('Item Code')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('item.name')
                    ->label('Item Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label('Available Quantity')
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('warehouse_id')
                    ->label('Filter by Warehouse')
                    ->relationship('location.warehouse', 'name'),

                Tables\Filters\SelectFilter::make('location_id')
                    ->label('Filter by Location')
                    ->relationship('location', 'name'),
                
                Tables\Filters\Filter::make('show_zero_quantity')
                    ->label('Show Zero Quantity Items')
                    ->query(fn (Builder $query) => $query->where('quantity', 0))
                    ->default(false)
                    ->hidden(),
            ]);
    }


    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockOverview::route('/'),
            'create' => Pages\CreateStockOverview::route('/create'),
        ];
    }
}
