<?php

namespace Albet\SanctumRefresh\Helpers;

use Albet\SanctumRefresh\Models\RefreshToken;
use Carbon\Carbon;

class CheckForRefreshToken
{
    public static function check(string $givenToken): bool
    {
        if (!str_contains($givenToken, '|')) {
            return false;
        }

        $tokenId = explode('|', $givenToken);
        $userToken = $tokenId[array_key_last($tokenId)];
        $tokenId = (int) $tokenId[array_key_first($tokenId)];

        if (!$tokenId) {
            return false;
        }

        $token = RefreshToken::find($tokenId);

        if (!$token || !hash_equals($token->token, hash('sha256', $userToken)) || Carbon::parse($token->expires_at)->lt(now())) {
            return false;
        }

        return true;
    }
}
