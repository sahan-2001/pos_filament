<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryItemResource\Pages;
use App\Models\InventoryItem;
use App\Models\Category;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions;


class InventoryItemResource extends Resource
{
    protected static ?string $model = InventoryItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Inventory Management'; 

     static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('item_code')
                    ->label('Item Code')
                    ->disabled()
                    ->default(fn () => self::generateItemCode()),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('category')
                    ->label('Category')
                    ->options(fn () => self::getCategoryOptions())
                    ->required(),
                Forms\Components\Textarea::make('special_note')
                    ->label('Special Note')
                    ->nullable(),
                Forms\Components\Select::make('uom')
                    ->label('Unit of Measure')
                    ->options([
                        'kg' => 'Kg',
                        'liters' => 'Liters',
                        'meters' => 'Meters',
                        'pcs' => 'Pcs',
                        // ...other units...
                    ])
                    ->required(),
                Forms\Components\TextInput::make('available_quantity')
                    ->hidden()
                    ->default(0)
                    ->numeric(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item_code')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('category')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('uom')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('available_quantity')->sortable()->searchable(),
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
                    ->visible(fn (InventoryItem $record) => auth()->user()->can('edit inventory items')),
                Tables\Actions\DeleteAction::make()
                ->visible(fn (InventoryItem $record) =>
                    auth()->user()->can('delete inventory items') &&
                    $record->available_quantity < 1
                ),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()->can('delete inventory items'))
                    ->before(function (Collection $records) {
                        $blocked = $records->filter(fn ($record) => $record->available_quantity >= 1);

                        if ($blocked->isNotEmpty()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Cannot delete selected items')
                                ->body('One or more items have available quantity and cannot be deleted.')
                                ->danger()
                                ->send();

                            abort(403, 'Deletion blocked: Items with available quantity ≥ 1');
                        }
                    }),
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
            'index' => Pages\ListInventoryItems::route('/'),
            'create' => Pages\CreateInventoryItem::route('/create'),
            'edit' => Pages\EditInventoryItem::route('/{record}/edit'),
        ];
    }

    protected static function generateItemCode(): string
    {
        $lastItem = InventoryItem::latest()->first();
        $nextId = $lastItem ? $lastItem->id + 1 : 1;
        $categoryCode = strtoupper(substr(request()->input('category', 'CAT'), 0, 3));
        return $categoryCode . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    protected static function getCategoryOptions(): array
    {
        return Category::pluck('name', 'name')->toArray();
    }

    public static function addCategory(array $data)
    {
        $categoryName = ucfirst($data['new_category']);
        if (!Category::where('name', $categoryName)->exists()) {
            Category::create(['name' => $categoryName]);
        }
    }
}