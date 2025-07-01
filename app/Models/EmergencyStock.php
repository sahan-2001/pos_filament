<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EmergencyStock extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'item_id',
        'quantity',
        'cost',
        'updated_date',
        'received_date',
        'created_by',
        'updated_by',
    ];

    // Spatie Log Config
    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'emergency_stock';

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
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('emergency_stock')
            ->setDescriptionForEvent(function (string $eventName) {
                $changes = $this->getDirty();
                $description = "Emergency Stock #{$this->id} was {$eventName}";

                if ($eventName === 'updated' && !empty($changes)) {
                    $description .= ". Changes: " . json_encode($changes);
                }

                $user = auth()->user();
                if ($user) {
                    $description .= " by {$user->name} (ID: {$user->id})";
                }

                return $description;
            });
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
