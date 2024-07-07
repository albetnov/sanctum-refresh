<?php

namespace Albet\SanctumRefresh;

use Albet\SanctumRefresh\Models\RefreshToken;
use Carbon\Carbon;

if (!function_exists(__NAMESPACE__ . '\checkRefreshToken')) {
    function checkRefreshToken(string $token): bool
    {
        if (!str_contains($token, '|')) return false;

        $chunks = explode('|', $token, 2);

        [$tokenId, $userToken] = $chunks;

        if (trim($userToken) === '' || trim($tokenId) === '') return false;

        $token = RefreshToken::find((int) $tokenId);

        // Check if token exists and valid
        if (!$token || !hash_equals($token->token, hash('sha256', $userToken))) return false;

        // Check if token is expired
        if (Carbon::parse($token->expires_at)->lt(now())) return false;

        return true;
    }
}
