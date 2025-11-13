<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DraftBillPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'draft_invoice_id',
        'order_total',
        'payment_type',
        'pay_amount',
        'cash_received',
        'cash_balance',
        'reference',
        'bank',
        'cheque_no',
        'remarks',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'order_total' => 'decimal:2',
        'pay_amount' => 'decimal:2',
        'cash_received' => 'decimal:2',
        'cash_balance' => 'decimal:2',
    ];

    public function draftInvoice()
    {
        return $this->belongsTo(DraftInvoice::class);
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
}