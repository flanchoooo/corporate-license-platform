<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleImportError extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_import_id',
        'row_number',
        'number_plate',
        'message',
        'row_payload',
    ];

    protected function casts(): array
    {
        return [
            'row_payload' => 'array',
        ];
    }

    public function import(): BelongsTo
    {
        return $this->belongsTo(VehicleImport::class, 'vehicle_import_id');
    }
}
