<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Order extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'customer_id',
        'user_id',
        'subtotal',
        'discount',
        'total',
        'status',
        'order_date',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'order_date' => 'datetime'
    ];

    // Fixed customer relationship - removed the extra parameter
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Fixed user relationship
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Removed duplicate userId() method as it's redundant with user()

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

    protected static function booted()
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
                $model->user_id = auth()->id();
                $model->updated_by = auth()->id();
                
                // Set order_date if not provided
                if (empty($model->order_date)) {
                    $model->order_date = now();
                }
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['customer_id', 'subtotal', 'discount', 'total', 'status', 'order_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('orders');
    }
}