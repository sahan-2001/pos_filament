<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PoInvoicePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_order_invoice_id',
        'payment_amount',
        'remaining_amount_before',
        'remaining_amount_after',
        'payment_method',
        'payment_reference',
        'notes',
        'paid_by',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'payment_amount' => 'decimal:2',
        'remaining_amount_before' => 'decimal:2',
        'remaining_amount_after' => 'decimal:2',
    ];

    public function purchaseOrderInvoice()
    {
        return $this->belongsTo(PurchaseOrderInvoice::class);
    }

    public function paidByUser()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    protected static function booted()
    {
        static::creating(function ($payment) {
            $payment->paid_by = auth()->id();
            $payment->paid_at = now();
        });
    }
}
