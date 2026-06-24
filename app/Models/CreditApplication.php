<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'vehicle_id',
        'corporate_id',
        'name',
        'surname',
        'national_id',
        'mobile_number',
        'address',
        'delivery_address',
        'delivery_mobile',
        'delivery_landmark',
        'status',
        'admin_notes',
        'approved_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function corporate(): BelongsTo
    {
        return $this->belongsTo(Corporate::class);
    }
}
