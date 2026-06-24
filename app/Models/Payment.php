<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'corporate_id',
        'method',
        'reference',
        'amount_cents',
        'status',
        'provider_payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'provider_payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function corporate(): BelongsTo
    {
        return $this->belongsTo(Corporate::class);
    }
}
