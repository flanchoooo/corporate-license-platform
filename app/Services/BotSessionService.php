<?php

namespace App\Services;

use App\Models\BotSession;
use Illuminate\Http\Request;

class BotSessionService
{
    public function current(Request $request): BotSession
    {
        $sessionKey = $request->session()->getId() ?: $request->ip();

        return BotSession::firstOrCreate(
            ['session_key' => $sessionKey],
            ['channel' => 'web', 'state' => 'menu']
        );
    }

    public function setState(BotSession $session, string $state, array $context = []): BotSession
    {
        $session->update([
            'state' => $state,
            'context' => array_merge($session->context ?? [], $context),
        ]);

        return $session->fresh();
    }

    public function message(BotSession $session, string $sender, string $message, array $payload = []): void
    {
        $session->messages()->create(compact('sender', 'message', 'payload'));
    }
}
