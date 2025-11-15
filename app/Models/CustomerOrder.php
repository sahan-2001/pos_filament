<?php
// app/Models/CustomerOrder.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CustomerOrder extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'order_id',
        'customer_id',
        'subtotal',
        'tax_amount',
        'shipping_cost',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'order_status',
        'payment_status',
        'payment_method',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_zip_code',
        'shipping_country',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_city',
        'billing_state',
        'billing_zip_code',
        'billing_country',
        'order_date',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'notes',
        'cancellation_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'order_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static $logAttributes = [
        'order_status',
        'payment_status',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'notes',
    ];

    protected static $logName = 'customer_order';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['order_status', 'payment_status', 'total_amount', 'paid_amount', 'remaining_amount', 'notes'])
            ->useLogName('customer_order')
            ->setDescriptionForEvent(fn(string $eventName) => "Customer Order {$this->order_id} has been {$eventName}");
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order_id)) {
                $model->order_id = 'ORD-' . date('Ymd') . '-' . str_pad(static::withTrashed()->count() + 1, 5, '0', STR_PAD_LEFT);
            }
            $model->created_by = auth()->id() ?? 1;
            
            // Set initial remaining amount
            if (empty($model->remaining_amount)) {
                $model->remaining_amount = $model->total_amount - $model->paid_amount;
            }
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id() ?? $model->updated_by ?? 1;
            
            // Auto-calculate remaining amount when paid_amount changes
            if ($model->isDirty('paid_amount')) {
                $model->remaining_amount = $model->total_amount - $model->paid_amount;
                
                // Auto-update payment status based on amounts
                if ($model->remaining_amount <= 0) {
                    $model->payment_status = 'paid';
                } elseif ($model->paid_amount > 0) {
                    $model->payment_status = 'partially_refunded';
                }
            }
        });

        static::saving(function ($model) {
            // Ensure remaining amount is calculated
            if (empty($model->remaining_amount) && !empty($model->total_amount)) {
                $model->remaining_amount = $model->total_amount - $model->paid_amount;
            }
        });
    }

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class); // You might want to create this model for payment tracking
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('order_status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('order_status', 'confirmed');
    }

    public function scopeProcessing($query)
    {
        return $query->where('order_status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('order_status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('order_status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('order_status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePendingPayment($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePartiallyPaid($query)
    {
        return $query->where('payment_status', 'partially_refunded');
    }

    public function scopeWithRemainingBalance($query)
    {
        return $query->where('remaining_amount', '>', 0);
    }

    // Methods
    public function calculateTotals(): void
    {
        $subtotal = $this->orderItems->sum('total_price');
        $this->subtotal = $subtotal;
        $this->total_amount = $subtotal + $this->tax_amount + $this->shipping_cost - $this->discount_amount;
        $this->remaining_amount = $this->total_amount - $this->paid_amount;
        $this->save();
    }

    public function addPayment(float $amount, string $method = null): bool
    {
        $this->paid_amount += $amount;
        $this->remaining_amount = $this->total_amount - $this->paid_amount;
        
        if ($method) {
            $this->payment_method = $method;
        }
        
        // Update payment status
        if ($this->remaining_amount <= 0) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = 'partially_refunded';
        }
        
        return $this->save();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->order_status, ['pending', 'confirmed', 'processing']);
    }

    public function markAsConfirmed(): bool
    {
        if ($this->order_status === 'pending') {
            $this->update([
                'order_status' => 'confirmed',
                'confirmed_at' => now(),
                'updated_by' => auth()->id() ?? 1,
            ]);
            return true;
        }
        return false;
    }

    public function markAsProcessing(): bool
    {
        if (in_array($this->order_status, ['confirmed', 'pending'])) {
            $this->update([
                'order_status' => 'processing',
                'updated_by' => auth()->id() ?? 1,
            ]);
            return true;
        }
        return false;
    }

    public function markAsShipped(): bool
    {
        if ($this->order_status === 'processing') {
            $this->update([
                'order_status' => 'shipped',
                'shipped_at' => now(),
                'updated_by' => auth()->id() ?? 1,
            ]);
            return true;
        }
        return false;
    }

    public function markAsDelivered(): bool
    {
        if ($this->order_status === 'shipped') {
            $this->update([
                'order_status' => 'delivered',
                'delivered_at' => now(),
                'updated_by' => auth()->id() ?? 1,
            ]);
            return true;
        }
        return false;
    }

    public function cancelOrder(string $reason = null): bool
    {
        if ($this->canBeCancelled()) {
            $this->update([
                'order_status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
                'updated_by' => auth()->id() ?? 1,
            ]);

            // Restore inventory for all order items
            $this->orderItems->each(function ($item) {
                if ($item->inventory_deducted) {
                    $item->restoreInventory();
                }
            });

            return true;
        }
        return false;
    }

    // Accessors
    public function getFormattedSubtotalAttribute(): string
    {
        return '₹' . number_format($this->subtotal, 2);
    }

    public function getFormattedTaxAmountAttribute(): string
    {
        return '₹' . number_format($this->tax_amount, 2);
    }

    public function getFormattedShippingCostAttribute(): string
    {
        return '₹' . number_format($this->shipping_cost, 2);
    }

    public function getFormattedDiscountAmountAttribute(): string
    {
        return '₹' . number_format($this->discount_amount, 2);
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return '₹' . number_format($this->total_amount, 2);
    }

    public function getFormattedPaidAmountAttribute(): string
    {
        return '₹' . number_format($this->paid_amount, 2);
    }

    public function getFormattedRemainingAmountAttribute(): string
    {
        return '₹' . number_format($this->remaining_amount, 2);
    }

    public function getItemsCountAttribute(): int
    {
        return $this->orderItems->sum('quantity');
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->remaining_amount <= 0;
    }

    public function getHasPaymentsAttribute(): bool
    {
        return $this->paid_amount > 0;
    }

    public function getPaymentProgressAttribute(): float
    {
        if ($this->total_amount <= 0) return 0;
        return ($this->paid_amount / $this->total_amount) * 100;
    }

    // Status helpers
    public function isPending(): bool
    {
        return $this->order_status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->order_status === 'confirmed';
    }

    public function isProcessing(): bool
    {
        return $this->order_status === 'processing';
    }

    public function isShipped(): bool
    {
        return $this->order_status === 'shipped';
    }

    public function isDelivered(): bool
    {
        return $this->order_status === 'delivered';
    }

    public function isCancelled(): bool
    {
        return $this->order_status === 'cancelled';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPartiallyPaid(): bool
    {
        return $this->payment_status === 'partially_refunded';
    }
}