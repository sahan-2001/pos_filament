<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Customer extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'name',
        'shop_name',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_zip_code',
        'shipping_state',
        'shipping_country',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_city',
        'billing_zip_code',
        'billing_state',
        'billing_country',
        'email',
        'phone_1',
        'phone_2',
        'remaining_balance',
        'requested_by',
        'approved_by',
        'created_by',
        'updated_by'
    ];

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    protected static $logAttributes = [
        'name',
        'shop_name',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_zip_code',
        'shipping_state',
        'shipping_country',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_city',
        'billing_zip_code',
        'billing_state',
        'billing_country',
        'email',
        'phone_1',
        'phone_2',
        'remaining_balance',
        'requested_by',
        'approved_by',
        'created_by',
        'updated_by'
    ];

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
    
    protected static $logName = 'customer';

    /**
     * Get the options for activity logging.
     *
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'shop_name',
                'shipping_address_line_1',
                'shipping_address_line_2',
                'shipping_city',
                'shipping_zip_code',
                'shipping_state',
                'shipping_country',
                'billing_address_line_1',
                'billing_address_line_2',
                'billing_city',
                'billing_zip_code',
                'billing_state',
                'billing_country',
                'email',
                'phone_1',
                'phone_2',
                'remaining_balance',
                'requested_by',
                'approved_by',
            ])
            ->useLogName('customer')
            ->setDescriptionForEvent(fn(string $eventName) => "Customer {$this->customer_id} has been {$eventName}");
    }
}