<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'corporate_id',
        'uploaded_by',
        'filename',
        'status',
        'total_rows',
        'imported_rows',
        'failed_rows',
    ];

    public function corporate(): BelongsTo
    {
        return $this->belongsTo(Corporate::class);
    }

    public function errors(): HasMany
    {
        return $this->hasMany(VehicleImportError::class);
    }
}
