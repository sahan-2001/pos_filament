<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryItemResource\Pages;
use App\Models\InventoryItem;
use App\Models\Category;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class InventoryItemResource extends Resource
{
    protected static ?string $model = InventoryItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Inventory Management';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Item Details')
                    ->schema([

                        Forms\Components\TextInput::make('item_code')
                            ->label('Item Code')
                            ->unique(),

                        Forms\Components\TextInput::make('name')
                            ->label('Item Name')
                            ->required(),

                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->relationship('categoryRelation', 'name')
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('uom')
                            ->label('Unit of Measure')
                            ->options([
                                'kg' => 'Kg',
                                'liters' => 'Liters',
                                'meters' => 'Meters',
                                'pcs' => 'Pcs',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('barcode')
                            ->numeric()
                            ->nullable(),

                        Forms\Components\TextInput::make('available_quantity')
                            ->numeric()
                            ->default(0)
                            ->hidden(), // user cannot manually edit
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([

                        Forms\Components\TextInput::make('moq')
                            ->numeric()
                            ->label('Minimum Order Quantity / Alert Qty')
                            ->nullable(),

                        Forms\Components\TextInput::make('max_stock')
                            ->numeric()
                            ->label('Maximum Stock Level')
                            ->nullable(),

                        Forms\Components\TextInput::make('market_price')
                            ->numeric()
                            ->label('Market Price')
                            ->step('0.01')
                            ->nullable(),

                        Forms\Components\TextInput::make('selling_price')
                            ->numeric()
                            ->label('Selling Price')
                            ->step('0.01'),

                        Forms\Components\TextInput::make('cost')
                            ->numeric()
                            ->label('Cost Price')
                            ->step('0.01'),

                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('items')
                            ->imageEditor()
                            ->nullable(),

                        Forms\Components\Textarea::make('special_note')
                            ->columnSpanFull()
                            ->nullable(),

                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item_code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('categoryRelation.name')
                    ->label('Category')
                    ->searchable()->sortable(),

                Tables\Columns\TextColumn::make('uom')->label('UOM')->sortable(),

                Tables\Columns\TextColumn::make('available_quantity')
                    ->label('Available Qty')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('categoryRelation', 'name'),

                Tables\Filters\SelectFilter::make('uom')
                    ->options([
                        'kg' => 'Kg',
                        'liters' => 'Liters',
                        'meters' => 'Meters',
                        'pcs' => 'Pcs',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => Auth::user()->can('edit inventory items')),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) =>
                        Auth::user()->can('delete inventory items') &&
                        $record->available_quantity < 1
                    ),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => Auth::user()->can('delete inventory items'))
                    ->before(function (Collection $records) {
                        $blocked = $records->filter(fn ($r) => $r->available_quantity >= 1);

                        if ($blocked->isNotEmpty()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Cannot delete selected items')
                                ->body('Some items have available quantity > 0')
                                ->danger()
                                ->send();

                            abort(403, 'Deletion blocked.');
                        }
                    }),
            ])
            ->recordUrl(null);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryItems::route('/'),
            'create' => Pages\CreateInventoryItem::route('/create'),
            'edit' => Pages\EditInventoryItem::route('/{record}/edit'),
        ];
    }
}
