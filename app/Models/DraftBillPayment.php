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
        'reference',
        'bank',
        'remarks',
        'created_by',
        'updated_by'
    ];

    public function draftInvoice()
    {
        return $this->belongsTo(DraftInvoice::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}