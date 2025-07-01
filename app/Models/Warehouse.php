<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    // Defining the fillable properties to allow mass assignment
    protected $fillable = [
        'name',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'city',
        'note',
        'capacity_length',
        'capacity_width',
        'capacity_height',
        'measurement_unit',
        'created_by',
        'updated_by',
    ];

    // Defining the date attributes for soft deletes
    protected $dates = ['deleted_at'];

    /**
     * Get the user that owns the warehouse.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the warehouse's full address as a string.
     */
    public function getFullAddressAttribute()
    {
        return $this->address_line_1 . ', ' .
               ($this->address_line_2 ? $this->address_line_2 . ', ' : '') .
               ($this->address_line_3 ? $this->address_line_3 . ', ' : '') .
               $this->city;
    }

    /**
     * Get the warehouse's capacity in the desired unit.
     */
    public function getCapacityAttribute()
    {
        return [
            'length' => $this->capacity_length,
            'width' => $this->capacity_width,
            'height' => $this->capacity_height,
            'measurement_unit' => $this->measurement_unit,
        ];
    }

    protected static $logAttributes = ['name', 'address_line_1', 'city', 'capacity_length', 'capacity_width', 'capacity_height', 'measurement_unit'];
    protected static $logName = 'warehouse';

    /**
     * Get the options for activity logging.
     *
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'address_line_1', 'city', 'capacity_length', 'capacity_width', 'capacity_height', 'measurement_unit'])
            ->useLogName('warehouse')
            ->setDescriptionForEvent(fn(string $eventName) => "Warehouse {$this->id} has been {$eventName} by User {$this->created_by}");
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