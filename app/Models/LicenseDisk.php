<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseDisk extends Model
{
    use HasFactory;

    protected $fillable = [
        'corporate_id',
        'vehicle_id',
        'quote_id',
        'reference_number',
        'radio_license_fee_cents',
        'insurance_fee_cents',
        'zinara_fee_cents',
        'arrears_cents',
        'total_paid_cents',
        'valid_from',
        'valid_until',
        'qr_payload',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_until' => 'date',
            'issued_at' => 'datetime',
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

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
