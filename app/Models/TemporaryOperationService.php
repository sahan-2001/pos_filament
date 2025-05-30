<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemporaryOperationService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'temporary_operation_id',
        'third_party_service_id',
    ];

    public function temporaryOperation()
    {
        return $this->belongsTo(TemporaryOperation::class);
    }

    public function service()
    {
        return $this->belongsTo(ThirdPartyService::class,);
    }

    public function thirdPartyService()
    {
        return $this->belongsTo(ThirdPartyService::class, 'third_party_service_id');
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
