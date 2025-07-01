<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'item_code',
        'name',
        'category',
        'special_note',
        'uom',
        'available_quantity',
        'moq',
        'max_stock',
        'image',
        'created_by',
        'updated_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate a unique item code
            $model->item_code = self::generateUniqueItemCode($model->category);
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

    /**
     * Generate a unique item code based on category
     */
    protected static function generateUniqueItemCode(string $category): string
    {
        $categoryCode = strtoupper(substr($category, 0, 3));
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $attempt++;
            $lastItem = self::where('item_code', 'like', $categoryCode.'%')
                ->orderBy('item_code', 'desc')
                ->first();

            if ($lastItem) {
                // Extract the numeric part and increment
                $numericPart = (int) substr($lastItem->item_code, 3);
                $nextNumber = $numericPart + 1;
            } else {
                $nextNumber = 1;
            }

            $newCode = $categoryCode . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Check if code already exists (unlikely but possible in race conditions)
            $exists = self::where('item_code', $newCode)->exists();

            if (!$exists) {
                return $newCode;
            }

            // If we're here, the code exists - try again with a higher number
            $nextNumber++;

        } while ($attempt < $maxAttempts);

        // If all attempts fail (extremely unlikely), fall back to UUID
        return $categoryCode . substr(Str::uuid()->toString(), 0, 4);
    }

    protected static $logAttributes = [
        'item_code',
        'name',
        'category',
        'special_note',
        'uom',
        'available_quantity',
        'moq',
        'max_stock',
        'image',
        'barcode',
        'created_by',
    ];

    protected static $logName = 'inventory_item';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'item_code',
                'name',
                'category',
                'special_note',
                'uom',
                'available_quantity',
                'moq',
                'max_stock',
                'image',
                'created_by', 
            ])
            ->useLogName('inventory_item')
            ->setDescriptionForEvent(fn(string $eventName) => "Inventory Item {$this->id} has been {$eventName}");
    }

    public function registerArrivalItems()
    {
        return $this->hasMany(RegisterArrivalItem::class, 'item_id');
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