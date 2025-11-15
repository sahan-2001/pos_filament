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
        'category_id', 
        'special_note',
        'uom',
        'available_quantity',
        'moq',
        'max_stock',
        'barcode',
        'market_price',
        'selling_price',
        'cost',
        'image',
        'created_by',
        'updated_by',
    ];

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

    // Add category relationship
    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Keep the old category method for backward compatibility
    public function getCategoryNameAttribute()
    {
        return $this->categoryRelation ? $this->categoryRelation->name : $this->category;
    }

    protected static $logAttributes = [
        'item_code',
        'name',
        'category',
        'category_id',
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
                'category_id',
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

    public function updateQuantity($newQuantity, $userId = null)
    {
        $this->available_quantity = $newQuantity;
        $this->updated_by = $userId ?? auth()->id() ?? $this->updated_by ?? 1;
        return $this->save();
    }

    public function decrementQuantity($quantity, $userId = null)
    {
        $this->available_quantity = $this->available_quantity - $quantity;
        $this->updated_by = $userId ?? auth()->id() ?? $this->updated_by ?? 1;
        return $this->save();
    }

    public function incrementQuantity($quantity, $userId = null)
    {
        $this->available_quantity = $this->available_quantity + $quantity;
        $this->updated_by = $userId ?? auth()->id() ?? $this->updated_by ?? 1;
        return $this->save();
    }
}