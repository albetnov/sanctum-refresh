<?php

namespace Albet\SanctumRefresh\Repositories;

use Albet\SanctumRefresh\Helpers;
use Albet\SanctumRefresh\Models\RefreshToken;

class RefreshTokenRepository
{
    public function revokeRefreshTokenFromTokenId(int $tokenId): bool
    {
        $find = RefreshToken::where('token_id', $tokenId)->first();
        if ($find) {
            $find->delete();

            return true;
        }

        return false;
    }

    public function revokeRefreshTokenFromToken(string $plainRefreshToken): bool
    {
        $refreshToken = Helpers::verifyRefreshToken($plainRefreshToken);

        if (! $refreshToken) {
            return false;
        }

        $refreshToken->delete();

        return true;
    }
}
