<?php

namespace Albet\SanctumRefresh\Repositories;

use Albet\SanctumRefresh\Helpers;
use Albet\SanctumRefresh\Models\RefreshToken;

class RefreshTokenRepository
{
    public function revokeFromTokenId(int $tokenId): bool
    {
        $find = RefreshToken::where('token_id', $tokenId)->first();

        if (!$find) return false;

        $find->delete();

        return true;
    }

    public function revokeFromTokenText(string $plainRefreshToken): bool
    {
        $tokenParts = Helpers::parseRefreshToken($plainRefreshToken);

        if (!$tokenParts) return false;

        $refreshToken = RefreshToken::check($tokenParts[1])->find($tokenParts[0]);

        if (! $refreshToken) return false;

        $refreshToken->delete();

        return true;
    }
}
