<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'order_id', // Add this line
        'user_id',
        'customer_id',
        'order_number',
        'subtotal',
        'discount',
        'total',
        'status',
        'order_date',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'order_date' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate unique order_id based on date
            if (empty($model->order_id)) {
                $model->order_id = self::generateOrderId();
            }
            
            // Set created_by if not set
            if (auth()->check() && empty($model->created_by)) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            // Set updated_by if not set
            if (auth()->check() && empty($model->updated_by)) {
                $model->updated_by = auth()->id();
            }
        });
    }

    /**
     * Generate unique order_id based on date (format: ORD-YYYYMMDD-XXXX)
     */
    protected static function generateOrderId(): string
    {
        $date = now()->format('Ymd');
        $prefix = 'ORD';
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $attempt++;
            
            // Get the last order for today
            $lastOrder = self::where('order_id', 'like', $prefix . '-' . $date . '-%')
                ->orderBy('order_id', 'desc')
                ->first();

            if ($lastOrder) {
                // Extract the sequence number and increment
                $lastId = $lastOrder->order_id;
                $sequence = (int) substr($lastId, -4) + 1;
            } else {
                $sequence = 1;
            }

            $newOrderId = $prefix . '-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // Check if order_id already exists (unlikely but possible in race conditions)
            $exists = self::where('order_id', $newOrderId)->exists();

            if (!$exists) {
                return $newOrderId;
            }

            // If we're here, the order_id exists - try again with a higher sequence
            $sequence++;

        } while ($attempt < $maxAttempts);

        // If all attempts fail (extremely unlikely), fall back to UUID
        return $prefix . '-' . $date . '-' . substr(Str::uuid()->toString(), 0, 8);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static $logAttributes = [
        'order_id',
        'user_id',
        'customer_id',
        'order_number',
        'subtotal',
        'discount',
        'total',
        'status',
        'order_date'
    ];

    protected static $logName = 'order';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'order_id',
                'user_id',
                'customer_id',
                'order_number',
                'subtotal',
                'discount',
                'total',
                'status',
                'order_date'
            ])
            ->useLogName('order')
            ->setDescriptionForEvent(fn(string $eventName) => "Order {$this->order_id} has been {$eventName}");
    }
}