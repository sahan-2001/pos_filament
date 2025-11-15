<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'return_number',
        'return_reason',
        'total_refund_amount',
        'return_date',
        'status',
        'processed_by',
        'notes'
    ];

    protected $casts = [
        'return_date' => 'datetime',
        'total_refund_amount' => 'decimal:2',
    ];

    /**
     * Get the order associated with the return.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who processed the return.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the items for the return order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ReturnOrderItem::class);
    }

    /**
     * Get the refund associated with the return order.
     */
    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }

    /**
     * Generate a unique return number.
     */
    public static function generateReturnNumber(): string
    {
        $count = static::count() + 1;
        return 'RET-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($returnOrder) {
            if (empty($returnOrder->return_number)) {
                $returnOrder->return_number = static::generateReturnNumber();
            }
        });
    }
}