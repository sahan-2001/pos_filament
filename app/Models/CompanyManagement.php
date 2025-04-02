<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyManagement extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'position',
        'appointed_date',
        'updated_by' 
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointed_date' => 'date',
    ];

    /**
     * Configure activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'position', 'appointed_date'])
            ->setDescriptionForEvent(function(string $eventName) {
                $updaterEmail = $this->updater->email ?? 'system';
                return "Management position {$eventName} by {$updaterEmail}";
            })
            ->useLogName('company_management');
    }

    /**
     * Relationship to Company
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relationship to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to User who last updated the record
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for specific positions
     */
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Get display name for the management member
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->user->name} - {$this->position}";
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}