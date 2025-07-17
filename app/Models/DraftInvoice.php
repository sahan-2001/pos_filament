<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DraftInvoice extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

        protected $fillable = ['customer_id', 'subtotal', 'discount', 'total', 'status', 'created_by', 'updated_by',];
        
        public function customer()
        {
            return $this->belongsTo(Customer::class, 'customer_id');
        }
        
        public function user()
        {
            return $this->belongsTo(User::class);
        }
        
        public function items()
        {
            return $this->hasMany(DraftInvoiceItem::class);
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
