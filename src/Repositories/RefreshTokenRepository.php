<?php

namespace Albet\SanctumRefresh\Repositories;

use Albet\SanctumRefresh\Helpers\CheckForRefreshToken;
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
        if (!CheckForRefreshToken::check($plainRefreshToken)) {
            return false;
        }

        $findTokenId = explode('|', $plainRefreshToken);
        $findTokenId = $findTokenId[array_key_first($findTokenId)];

        return RefreshToken::find($findTokenId)->delete();
    }
}
