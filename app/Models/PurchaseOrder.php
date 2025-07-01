<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class PurchaseOrder extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'supplier_id',
        'wanted_date',
        'special_note',
        'status', 
        'grand_total',
        'random_code', 
        'created_by',
        'updated_by',
    ];

    protected static function booted()
    {
        static::creating(function ($purchaseOrder) {
            $purchaseOrder->random_code = '';
            for ($i = 0; $i < 16; $i++) {
                $purchaseOrder->random_code .= mt_rand(0, 9);
            }
        });

        static::creating(function ($model) {
            $model->created_by = auth()->id();
            $model->updated_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

    public function recalculateGrandTotal()
    {
        $this->grand_total = $this->items()->sum('total_sale');
        $this->saveQuietly(); 
    }


    protected static $logName = 'purchase_order';

    /**
     * Get the options for activity logging.
     *
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'provider_type',
                'provider_id',
                'wanted_date',
                'special_note',
                'status', 
                'grand_total'
            ])
            ->useLogName('purchase_order')
            ->setDescriptionForEvent(function (string $eventName) {
                $userEmail = $this->user ? $this->user->email : 'unknown';
                $description = "Purchase Order {$this->id} has been {$eventName} by User {$this->user_id}";

                // Add updated status to the description if status is changed
                if ($this->isDirty('status')) {
                    $description .= ". New status: {$this->status}";
                }

                return $description;
            });
    }
    

    /**
     * Get the user that owns the purchase order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the purchase order.
     */
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function supplierAdvanceInvoices()
    {
        return $this->hasMany(SupplierAdvanceInvoice::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

}