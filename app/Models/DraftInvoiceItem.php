<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DraftInvoiceItem extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'draft_invoice_id', 'product_id', 'quantity', 
        'cost_price', 'selling_price', 'line_total', 'created_by',
        'updated_by',
    ];
    
    public function draftInvoice()
    {
        return $this->belongsTo(DraftInvoice::class);
    }
    
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'product_id');
    }

    protected static function booted()
        {
            static::creating(function ($model) {
                if (auth()->check()) {
                    $model->created_by = auth()->id();
                    $model->updated_by = auth()->id();
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
            ->logOnly(['invoice_number', 'customer_id', 'subtotal', 'discount', 'total', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('draft-invoices');
    }
}
