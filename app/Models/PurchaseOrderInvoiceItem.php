<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderInvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_order_invoice_id',
        'item_id',
        'stored_quantity',
        'location_id',
        'unit_price',
        'total',
        'created_by',
        'updated_by',
    ];

    public function invoice()
    {
        return $this->belongsTo(PurchaseOrderInvoice::class, 'purchase_order_invoice_id');
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function location()
    {
        return $this->belongsTo(InventoryLocation::class);
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
