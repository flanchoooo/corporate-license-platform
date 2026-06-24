<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcoCashTopUp extends Model
{
    use HasFactory;

    protected $table = 'ecocash_top_ups';

    protected $fillable = [
        'corporate_id',
        'wallet_id',
        'initiated_by',
        'transaction_reference',
        'mobile_number',
        'amount_cents',
        'status',
        'provider_payload',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'provider_payload' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function corporate(): BelongsTo
    {
        return $this->belongsTo(Corporate::class);
    }
}
