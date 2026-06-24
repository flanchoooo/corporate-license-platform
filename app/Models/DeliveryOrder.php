<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'vehicle_id',
        'license_disk_id',
        'credit_application_id',
        'rider_user_id',
        'delivery_address',
        'contact_mobile',
        'landmark',
        'status',
        'assigned_at',
        'delivered_at',
        'failed_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'delivered_at' => 'datetime',
            'failed_at' => 'datetime',
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

    public function licenseDisk(): BelongsTo
    {
        return $this->belongsTo(LicenseDisk::class);
    }

    public function creditApplication(): BelongsTo
    {
        return $this->belongsTo(CreditApplication::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rider_user_id');
    }
}
