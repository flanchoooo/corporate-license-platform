<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BotMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'bot_session_id',
        'sender',
        'message',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(BotSession::class, 'bot_session_id');
    }
}
