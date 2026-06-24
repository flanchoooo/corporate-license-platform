<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Arrear extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'description',
        'amount_cents',
        'status',
        'settled_at',
    ];

    protected function casts(): array
    {
        return [
            'settled_at' => 'datetime',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
