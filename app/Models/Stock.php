<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Stock extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'item_id',
        'quantity',
        'cost',
        'purchase_order_id', 
        'created_by',
        'updated_by',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }


    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($stock) {
            $stock->updateItemAvailableQuantity();
        });

        static::deleted(function ($stock) {
            $stock->updateItemAvailableQuantity();
        });
    }

    public function updateItemAvailableQuantity()
    {
        $item = $this->item; 
        if ($item) {
            $sum = self::where('item_id', $item->id)->sum('quantity');

            $item->available_quantity = $sum;
            $item->saveQuietly();
        }
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = auth()->id();
            $model->updated_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('stock')
            ->setDescriptionForEvent(function (string $eventName) {
                $user = auth()->user();
                $userInfo = $user ? " by {$user->name} (ID: {$user->id})" : "";
                return "Stock #{$this->id} has been {$eventName}{$userInfo}";
            });
    }
}
