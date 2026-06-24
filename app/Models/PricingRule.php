<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'fee_type',
        'min_cc',
        'max_cc',
        'amount_cents',
        'percentage',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
