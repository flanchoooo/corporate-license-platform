<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'corporate_id',
        'vehicle_id',
        'created_by',
        'quote_number',
        'status',
        'subtotal_cents',
        'total_cents',
        'expires_at',
        'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'purchased_at' => 'datetime',
        ];
    }

    public function corporate(): BelongsTo
    {
        return $this->belongsTo(Corporate::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }
}
