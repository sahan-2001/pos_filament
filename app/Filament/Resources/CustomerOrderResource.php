<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerOrderResource\Pages;
use App\Filament\Resources\CustomerOrderResource\RelationManagers;
use App\Models\CustomerOrder;
use App\Models\InventoryItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class CustomerOrderResource extends Resource
{
    protected static ?string $model = CustomerOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('CustomerOrderTabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General & Dates')
                            ->schema([
                                Forms\Components\Section::make('General Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('order_id')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->default('CO-' . date('Ymd') . '-' . str_pad(CustomerOrder::count() + 1, 5, '0', STR_PAD_LEFT))
                                            ->disabled()
                                            ->dehydrated(),

                                        Forms\Components\Select::make('customer_id')
                                            ->relationship('customer', 'name')
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->customer_id} | {$record->name}")
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $customer = \App\Models\Customer::where('customer_id', $state)->first();

                                                    if ($customer) {
                                                        $set('customer_phone', $customer->phone_1);
                                                        $set('customer_email', $customer->email);
                                                        $set('customer_balance', $customer->remaining_balance);

                                                        // Shipping
                                                        $set('shipping_address_line_1', $customer->shipping_address_line_1);
                                                        $set('shipping_address_line_2', $customer->shipping_address_line_2);
                                                        $set('shipping_city', $customer->shipping_city);
                                                        $set('shipping_state', $customer->shipping_state);
                                                        $set('shipping_zip_code', $customer->shipping_zip_code);
                                                        $set('shipping_country', $customer->shipping_country);

                                                        // Billing
                                                        $set('billing_address_line_1', $customer->billing_address_line_1);
                                                        $set('billing_address_line_2', $customer->billing_address_line_2);
                                                        $set('billing_city', $customer->billing_city);
                                                        $set('billing_state', $customer->billing_state);
                                                        $set('billing_zip_code', $customer->billing_zip_code);
                                                        $set('billing_country', $customer->billing_country);
                                                    }
                                                }
                                            }),

                                        Forms\Components\TextInput::make('customer_phone')
                                            ->label('Customer Phone')
                                            ->disabled()
                                            ->dehydrated(false),

                                        Forms\Components\TextInput::make('customer_email')
                                            ->label('Customer Email')
                                            ->disabled()
                                            ->dehydrated(false),

                                        Forms\Components\TextInput::make('customer_balance')
                                            ->label('Remaining Balance of Customer')
                                            ->prefix('Rs.')
                                            ->disabled()
                                            ->dehydrated(false),

                                        Forms\Components\Select::make('payment_method')
                                            ->options([
                                                'cash' => 'Cash',
                                                'credit_card' => 'Credit Card',
                                                'debit_card' => 'Debit Card',
                                                'bank_transfer' => 'Bank Transfer',
                                                'digital_wallet' => 'Digital Wallet',
                                            ])
                                            ->label('Preferred Payment Method')
                                            ->native(false)
                                            ->default('cash'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Date Milestones')
                                    ->schema([
                                        Forms\Components\DatePicker::make('ordered_date')
                                            ->required()
                                            ->default(today()),

                                        Forms\Components\DatePicker::make('wanted_delivery_date_(customer_requested)')
                                            ->required()
                                            ->minDate(today()),

                                        Forms\Components\DatePicker::make('Planned_delivery_date')
                                            ->minDate(today()),

                                        Forms\Components\DatePicker::make('prommissed_delivery_date')
                                            ->minDate(today()),

                                        Forms\Components\DatePicker::make('actual_delivery_date')
                                            ->minDate(today()),

                                        Forms\Components\DatePicker::make('order_creation_date')
                                            ->default(today())
                                            ->dehydrated(false)
                                            ->disabled(),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Financial Details')
                            ->schema([
                                Forms\Components\Section::make('Order Items')
                                    ->schema([
                                        Forms\Components\Repeater::make('orderItems')
                                            ->relationship('orderItems')
                                            ->columns(7)
                                            ->columnSpanFull()
                                            ->reactive() 
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                self::calculateOrderTotals($set, $get); // calculate summary after any row change
                                            })
                                            ->schema([
                                                // --- Inventory Item Select ---
                                                Forms\Components\Select::make('inventory_item_id')
                                                    ->label('Item')
                                                    ->relationship(
                                                        name: 'inventoryItem',
                                                        titleAttribute: 'name',
                                                        modifyQueryUsing: fn ($query) => $query->where('available_quantity', '>', 0)
                                                    )
                                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->item_code} | {$record->name}")
                                                    ->searchable(['item_code', 'name'])
                                                    ->preload()
                                                    ->required()
                                                    ->columnSpan(3)
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        if ($state) {
                                                            $item = \App\Models\InventoryItem::find($state);
                                                            if ($item) {
                                                                $set('item_code', $item->item_code);
                                                                $set('item_name', $item->name);
                                                                $set('market_price', $item->market_price);
                                                                $set('selling_price', $item->selling_price);
                                                                $set('available_quantity', $item->available_quantity);
                                                                $set('quantity', 1);
                                                                $set('item_subtotal', $item->selling_price * 1);
                                                                $set('item_discount', 0);
                                                                $set('additional_cost', 0);
                                                                
                                                                // Calculate line total
                                                                $lineTotal = ($item->selling_price * 1) - (($item->selling_price * 1) * 0 / 100);
                                                                $set('line_total', $lineTotal);
                                                            }
                                                        } else {
                                                            $set('item_code', null);
                                                            $set('item_name', null);
                                                            $set('market_price', 0);
                                                            $set('selling_price', 0);
                                                            $set('available_quantity', 0);
                                                            $set('quantity', 0);
                                                            $set('item_subtotal', 0);
                                                            $set('item_discount', 0);
                                                            $set('additional_cost', 0);
                                                            $set('line_total', 0);
                                                        }

                                                        self::calculateOrderTotals($set, $get);
                                                    }),

                                                // --- Available Quantity ---
                                                Forms\Components\TextInput::make('available_quantity')
                                                    ->label('Available Qty')
                                                    ->numeric()
                                                    ->disabled()
                                                    ->columnSpan(2)
                                                    ->dehydrated(false),

                                                // --- Quantity ---
                                                Forms\Components\TextInput::make('quantity')
                                                    ->label('Order Qty')
                                                    ->numeric()
                                                    ->required()
                                                    ->minValue(1)
                                                    ->columnSpan(2)
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get, $component) {
                                                        $availableQty = $get('available_quantity') ?? 0;
                                                        $sellingPrice = $get('selling_price') ?? 0;

                                                        if ($state > $availableQty) {
                                                            \Filament\Notifications\Notification::make()
                                                                ->title('Quantity Exceeded')
                                                                ->body("You cannot order more than available quantity ({$availableQty}).")
                                                                ->danger()
                                                                ->duration(5000)
                                                                ->send();

                                                            $state = $availableQty > 0 ? $availableQty : 1;
                                                            $set('quantity', $state);
                                                        }

                                                        // Update subtotal
                                                        $itemSubtotal = $state * $sellingPrice;
                                                        $set('item_subtotal', $itemSubtotal);

                                                        // Recalculate line total
                                                        $discountPercent = $get('item_discount') ?? 0;
                                                        $additionalCost = $get('additional_cost') ?? 0;
                                                        $discountAmount = ($discountPercent / 100) * $itemSubtotal;
                                                        $lineTotal = ($itemSubtotal + $additionalCost) - $discountAmount;
                                                        
                                                        $set('line_total', $lineTotal);

                                                        self::calculateOrderTotals($set, $get);
                                                    }),

                                                // --- Market Price ---
                                                Forms\Components\TextInput::make('market_price')
                                                    ->label('Market Price')
                                                    ->numeric()
                                                    ->prefix('Rs.')
                                                    ->disabled()
                                                    ->columnSpan(2)
                                                    ->dehydrated(false),

                                                // --- Selling Price ---
                                                Forms\Components\TextInput::make('selling_price')
                                                    ->label('Our Selling Price')
                                                    ->numeric()
                                                    ->prefix('Rs.')
                                                    ->required()
                                                    ->columnSpan(2)
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        $quantity = $get('quantity') ?? 1;
                                                        
                                                        // Update subtotal
                                                        $itemSubtotal = $quantity * $state;
                                                        $set('item_subtotal', $itemSubtotal);

                                                        // Recalculate line total
                                                        $discountPercent = $get('item_discount') ?? 0;
                                                        $additionalCost = $get('additional_cost') ?? 0;
                                                        $discountAmount = ($discountPercent / 100) * $itemSubtotal;
                                                        $lineTotal = ($itemSubtotal + $additionalCost) - $discountAmount;
                                                        
                                                        $set('line_total', $lineTotal);
                                                        
                                                        self::calculateOrderTotals($set, $get);
                                                    }),

                                                // --- Item Subtotal ---
                                                Forms\Components\TextInput::make('item_subtotal')
                                                    ->label('Item Subtotal')
                                                    ->numeric()
                                                    ->prefix('Rs.')
                                                    ->disabled()
                                                    ->columnSpan(3)
                                                    ->dehydrated(false),

                                                // --- Item Discount (%) ---
                                                Forms\Components\TextInput::make('item_discount')
                                                    ->label('Item Discount (%)')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(100)
                                                    ->default(0)
                                                    ->suffix('%')
                                                    ->columnSpan(2)
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        $subtotal = $get('item_subtotal') ?? 0;
                                                        $additional = $get('additional_cost') ?? 0;

                                                        // Discount in amount
                                                        $discountAmount = ($state / 100) * $subtotal;

                                                        // Line total = subtotal + additional cost - discount
                                                        $lineTotal = ($subtotal + $additional) - $discountAmount;
                                                        $set('line_total', $lineTotal);

                                                        self::calculateOrderTotals($set, $get);
                                                    }),

                                                // --- Additional Cost ---
                                                Forms\Components\TextInput::make('additional_cost')
                                                    ->label('Additional Cost')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->prefix('Rs.')
                                                    ->columnSpan(2)
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        $subtotal = $get('item_subtotal') ?? 0;
                                                        $discountPercent = $get('item_discount') ?? 0;

                                                        // Discount in amount based on percentage
                                                        $discountAmount = ($discountPercent / 100) * $subtotal;

                                                        // Line total = subtotal + additional cost - discount
                                                        $lineTotal = ($subtotal + $state) - $discountAmount;
                                                        $set('line_total', $lineTotal);

                                                        self::calculateOrderTotals($set, $get);
                                                    }),

                                                // --- Line Total ---
                                                Forms\Components\TextInput::make('line_total')
                                                    ->label('Line Total')
                                                    ->numeric()
                                                    ->prefix('Rs.')
                                                    ->disabled()
                                                    ->columnSpan(3)
                                                    ->dehydrated(false),
                                            ])
                                            ->deleteAction(
                                                fn ($action) => $action->after(fn ($state, callable $set, callable $get) =>
                                                    self::calculateOrderTotals($set, $get)
                                                ),
                                            )
                                    ])
                                    ->columnSpanFull(),

                                // --- Order Summary Section ---
                                Forms\Components\Section::make('Order Summary')
                                    ->schema([
                                        Forms\Components\TextInput::make('subtotal')
                                            ->label('Subtotal (Items)')
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->reactive()
                                            ->afterStateHydrated(function ($state, callable $set, callable $get) {
                                                self::calculateOrderTotals($set, $get);
                                            }),

                                        Forms\Components\TextInput::make('discount_amount')
                                            ->label('Total Discount')
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->reactive(),

                                        Forms\Components\TextInput::make('additional_cost_total')
                                            ->label('Total Additional Cost')
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->reactive(),

                                        Forms\Components\TextInput::make('net_subtotal')
                                            ->label('Net Subtotal')
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->reactive(),

                                        Forms\Components\TextInput::make('tax_amount')
                                            ->label('Tax Amount')
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->default(0)
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                self::calculateFinalTotal($set, $get);
                                            }),

                                        Forms\Components\TextInput::make('shipping_cost')
                                            ->label('Shipping Cost')
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->default(0)
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                self::calculateFinalTotal($set, $get);
                                            }),

                                        Forms\Components\TextInput::make('total_amount')
                                            ->label('Total Amount')
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->reactive(),

                                        Forms\Components\TextInput::make('paid_amount')
                                            ->label('Paid Amount')
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->default(0)
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $totalAmount = floatval($get('total_amount') ?? 0);
                                                $set('remaining_amount', max(0, $totalAmount - $state));
                                            }),

                                        Forms\Components\TextInput::make('remaining_amount')
                                            ->label('Remaining Amount')
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->disabled()
                                            ->dehydrated(false),
                                    ])
                                    ->columns(3)
                            ]),

                        Forms\Components\Tabs\Tab::make('Shipping & Billing')
                            ->schema([
                                Forms\Components\Section::make('Shipping Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('shipping_address_line_1')
                                            ->label('Shipping Address Line 1')
                                            ->maxLength(255)
                                            ->required()
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('shipping_address_line_2')
                                            ->label('Shipping Address Line 2')
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('shipping_city')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('shipping_state')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('shipping_zip_code')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\Select::make('shipping_country')
                                            ->options(\App\Helpers\Countries::all())
                                            ->searchable()
                                            ->required()
                                            ->native(false),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Billing Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('billing_address_line_1')
                                            ->label('Billing Address Line 1')
                                            ->maxLength(255)
                                            ->required()
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('billing_address_line_2')
                                            ->label('Billing Address Line 2')
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('billing_city')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('billing_state')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('billing_zip_code')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\Select::make('billing_country')
                                            ->options(\App\Helpers\Countries::all())
                                            ->required()
                                            ->searchable()
                                            ->native(false),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Additional Information')
                                    ->schema([
                                        Forms\Components\Textarea::make('notes')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected static function calculateOrderTotals(callable $set, callable $get)
    {
        $items = $get('orderItems') ?? [];

        $subtotal = 0;
        $discountTotal = 0;
        $additionalCostTotal = 0;

        foreach ($items as $item) {
            $itemSubtotal = floatval($item['item_subtotal'] ?? 0);
            $itemDiscountPercent = floatval($item['item_discount'] ?? 0);
            $additionalCost = floatval($item['additional_cost'] ?? 0);

            $discountAmount = ($itemDiscountPercent / 100) * $itemSubtotal;

            $subtotal += $itemSubtotal;
            $discountTotal += $discountAmount;
            $additionalCostTotal += $additionalCost;
        }

        $netSubtotal = $subtotal - $discountTotal + $additionalCostTotal;

        $set('subtotal', $subtotal);
        $set('discount_amount', $discountTotal);
        $set('additional_cost_total', $additionalCostTotal);
        $set('net_subtotal', $netSubtotal);

        self::calculateFinalTotal($set, $get);
    }

    protected static function calculateFinalTotal(callable $set, callable $get)
    {
        $netSubtotal = floatval($get('net_subtotal') ?? 0);
        $taxAmount = floatval($get('tax_amount') ?? 0);
        $shippingCost = floatval($get('shipping_cost') ?? 0);

        $totalAmount = $netSubtotal + $taxAmount + $shippingCost;
        $set('total_amount', $totalAmount);

        $paidAmount = floatval($get('paid_amount') ?? 0);
        $remainingAmount = max(0, $totalAmount - $paidAmount);
        $set('remaining_amount', $remainingAmount);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'confirmed' => 'info',
                        'processing' => 'warning',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        'refunded' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'warning',
                        'partially_refunded' => 'info',
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_amount')
                    ->money('INR')
                    ->sortable()
                    ->color(fn ($record) => $record->remaining_amount > 0 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('order_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('order_status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                        'partially_refunded' => 'Partially Refunded',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('markConfirmed')
                    ->label('Confirm')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (CustomerOrder $record) => $record->markAsConfirmed())
                    ->visible(fn (CustomerOrder $record) => $record->order_status === 'pending')
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('markProcessing')
                    ->label('Process')
                    ->icon('heroicon-o-cog')
                    ->color('warning')
                    ->action(fn (CustomerOrder $record) => $record->markAsProcessing())
                    ->visible(fn (CustomerOrder $record) => in_array($record->order_status, ['confirmed', 'pending']))
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('markShipped')
                    ->label('Ship')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->action(fn (CustomerOrder $record) => $record->markAsShipped())
                    ->visible(fn (CustomerOrder $record) => $record->order_status === 'processing')
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('markDelivered')
                    ->label('Deliver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (CustomerOrder $record) => $record->markAsDelivered())
                    ->visible(fn (CustomerOrder $record) => $record->order_status === 'shipped')
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('addPayment')
                    ->label('Add Payment')
                    ->icon('heroicon-o-currency-rupee')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->prefix('₹')
                            ->minValue(0.01)
                            ->maxValue(fn (CustomerOrder $record) => $record->remaining_amount),
                        Forms\Components\Select::make('method')
                            ->options([
                                'cash' => 'Cash',
                                'credit_card' => 'Credit Card',
                                'debit_card' => 'Debit Card',
                                'bank_transfer' => 'Bank Transfer',
                                'digital_wallet' => 'Digital Wallet',
                            ])
                            ->required(),
                    ])
                    ->action(function (CustomerOrder $record, array $data): void {
                        $record->addPayment($data['amount'], $data['method']);
                    })
                    ->visible(fn (CustomerOrder $record) => $record->remaining_amount > 0),
                Tables\Actions\Action::make('cancelOrder')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Cancellation Reason')
                            ->required(),
                    ])
                    ->action(function (CustomerOrder $record, array $data): void {
                        $record->cancelOrder($data['reason']);
                    })
                    ->visible(fn (CustomerOrder $record) => $record->canBeCancelled()),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('order_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerOrders::route('/'),
            'create' => Pages\CreateCustomerOrder::route('/create'),
            'edit' => Pages\EditCustomerOrder::route('/{record}/edit'),
        ];
    }
}