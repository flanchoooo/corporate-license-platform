<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CbzDirectPayment extends Model
{
    use HasFactory;

    protected $table = 'cbz_direct_payments';

    protected $fillable = [
        'corporate_id',
        'wallet_id',
        'initiated_by',
        'payment_reference',
        'bank_reference',
        'payer_name',
        'account_number',
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
