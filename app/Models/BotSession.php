<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BotSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_key',
        'channel',
        'current_menu',
        'number_plate',
        'state',
        'context',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(BotMessage::class);
    }
}
