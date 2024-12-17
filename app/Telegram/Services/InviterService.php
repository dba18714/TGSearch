<?php

namespace App\Telegram\Services;

use App\Models\User;

class InviterService
{
    public static function getInviterFromStartCommand(string $text): User|false
    {
        if ($text && str_starts_with($text, '/start')) {
            $parts = explode(' ', $text, 2);
            if (isset($parts[1])) {
                $inviter_tg_id = (int)$parts[1];
                if (! $inviter_tg_id) return false;

                $inviter = User::where('tg_id', $inviter_tg_id)->first();
                if ($inviter->exists) {
                    return $inviter;
                }
            }
        }
        return false;
    }
}