<?php

namespace Albet\SanctumRefresh\Repositories;

use Albet\SanctumRefresh\Exceptions\InvalidTokenException;
use Albet\SanctumRefresh\Helpers\CheckForRefreshToken;
use Albet\SanctumRefresh\Models\RefreshToken;

class RefreshTokenRepository
{
    public function revokeRefreshTokenFromTokenId(int $tokenId): bool
    {
        $find = RefreshToken::where('token_id', $tokenId)->first();
        if($find) {
            $find->delete();
            return true;
        }

        return false;
    }

    /**
     * @throws InvalidTokenException
     */
    public function revokeRefreshTokenFromToken(string $plainRefreshToken): void
    {
       CheckForRefreshToken::check($plainRefreshToken);

       $findTokenId = explode("|", $plainRefreshToken);
       $findTokenId = $findTokenId[array_key_first($findTokenId)];

       RefreshToken::find($findTokenId)->delete();
    }
}
