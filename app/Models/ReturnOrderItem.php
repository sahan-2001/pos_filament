<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_order_id',
        'order_item_id',
        'product_id',
        'quantity',
        'unit_price',
        'refund_amount',
        'reason'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'refund_amount' => 'decimal:2',
    ];

    /**
     * Get the return order that owns the item.
     */
    public function returnOrder(): BelongsTo
    {
        return $this->belongsTo(ReturnOrder::class);
    }

    /**
     * Get the original order item.
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the product being returned.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'product_id');
    }

    /**
     * Get the inventory item.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'product_id');
    }
}