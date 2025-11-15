<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_order_id',
        'order_id',
        'amount',
        'refund_method',
        'status',
        'refund_date',
        'processed_by',
        'reference_number',
        'notes'
    ];

    protected $casts = [
        'refund_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the return order associated with the refund.
     */
    public function returnOrder(): BelongsTo
    {
        return $this->belongsTo(ReturnOrder::class);
    }

    /**
     * Get the order associated with the refund.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who processed the refund.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Generate a unique reference number.
     */
    public static function generateReferenceNumber(): string
    {
        $count = static::count() + 1;
        return 'REF-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($refund) {
            if (empty($refund->reference_number)) {
                $refund->reference_number = static::generateReferenceNumber();
            }
        });
    }
}