<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\InventoryLocation;
use App\Models\InventoryItem;

class RegisterArrival extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'purchase_order_id',
        'received_date',
        'invoice_number',
        'image_of_invoice',
        'note',
        'created_by',
        'updated_by',
    ];

    public function items()
    {
        return $this->hasMany(RegisterArrivalItem::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }


    /**
     * Configure activity logging options.
     *
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'purchase_order_id',
                'location_id',
                'received_date',
                'invoice_number',
                'note',
            ])
            ->useLogName('register_arrival')
            ->setDescriptionForEvent(function (string $eventName) {
                return "Register Arrival record (ID: {$this->id}, Purchase Order ID: {$this->purchase_order_id}) has been {$eventName}.";
            });
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

}