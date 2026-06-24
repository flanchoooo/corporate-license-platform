<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'corporate_id',
        'number_plate',
        'engine_capacity',
        'make',
        'model',
        'year',
        'vehicle_type',
        'chassis_number',
        'vin',
        'fuel_type',
        'owner_name',
        'last_license_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'last_license_expires_at' => 'date',
        ];
    }

    public function corporate(): BelongsTo
    {
        return $this->belongsTo(Corporate::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function licenseDisks(): HasMany
    {
        return $this->hasMany(LicenseDisk::class);
    }

    public function scopeExpiringSoon(Builder $query): Builder
    {
        return $query->whereBetween('last_license_expires_at', [now()->toDateString(), now()->addDays(30)->toDateString()]);
    }
}
