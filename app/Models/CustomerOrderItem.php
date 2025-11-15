<?php
// app/Models/CustomerOrderItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CustomerOrderItem extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'customer_order_id',
        'inventory_item_id',
        'item_code',
        'item_name',
        'category',
        'uom',
        'special_note',
        'unit_price',
        'cost_price',
        'quantity',
        'discount_amount',
        'tax_amount',
        'total_price',
        'inventory_deducted',
        'inventory_deducted_at',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'inventory_deducted' => 'boolean',
        'inventory_deducted_at' => 'datetime',
    ];

    protected static $logAttributes = [
        'quantity',
        'unit_price',
        'total_price',
        'inventory_deducted',
    ];

    protected static $logName = 'customer_order_item';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['quantity', 'unit_price', 'total_price', 'inventory_deducted'])
            ->useLogName('customer_order_item')
            ->setDescriptionForEvent(fn(string $eventName) => "Order Item {$this->id} for Order {$this->customerOrder->order_number} has been {$eventName}");
    }

    // Relationships
    public function customerOrder(): BelongsTo
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    // Methods
    public function calculateTotalPrice(): void
    {
        $this->total_price = ($this->unit_price * $this->quantity) - $this->discount_amount + $this->tax_amount;
    }

    public function deductFromInventory(): bool
    {
        if ($this->inventory_deducted) {
            return true; // Already deducted
        }

        $inventoryItem = $this->inventoryItem;
        
        if ($inventoryItem->available_quantity >= $this->quantity) {
            // Use the decrementQuantity method from InventoryItem model
            $inventoryItem->decrementQuantity($this->quantity);
            
            $this->update([
                'inventory_deducted' => true,
                'inventory_deducted_at' => now(),
            ]);
            
            return true;
        }
        
        return false; // Insufficient inventory
    }

    public function restoreInventory(): bool
    {
        if (!$this->inventory_deducted) {
            return true; // Nothing to restore
        }

        $inventoryItem = $this->inventoryItem;
        // Use the incrementQuantity method from InventoryItem model
        $inventoryItem->incrementQuantity($this->quantity);
        
        $this->update([
            'inventory_deducted' => false,
            'inventory_deducted_at' => null,
        ]);
        
        return true;
    }

    public function getAvailableInventoryAttribute(): int
    {
        return $this->inventoryItem->available_quantity;
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return '₹' . number_format($this->unit_price, 2);
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return '₹' . number_format($this->total_price, 2);
    }

    // Automatic total price calculation
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->calculateTotalPrice();
        });

        static::created(function ($model) {
            $model->customerOrder->calculateTotals();
        });

        static::updated(function ($model) {
            $model->customerOrder->calculateTotals();
        });

        static::deleted(function ($model) {
            $model->customerOrder->calculateTotals();
        });
    }
}