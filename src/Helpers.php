<?php

namespace Albet\SanctumRefresh;

use Albet\SanctumRefresh\Exceptions\SanctumRefreshException;
use Albet\SanctumRefresh\Models\RefreshToken;
use Carbon\Carbon;

class Helpers
{
    const TOKEN_SEPERATOR = '|';

    /**
     * Parse the refresh token from string to token parts
     * return `false` on error.
     */
    public static function parseRefreshToken(string $plainRefreshToken): array|false
    {
        if (! str_contains($plainRefreshToken, self::TOKEN_SEPERATOR)) {
            return false;
        }

        $tokenParts = explode(self::TOKEN_SEPERATOR, $plainRefreshToken, 2);

        if (count($tokenParts) < 2) {
            return false;
        }

        // cast the id to int.
        $tokenParts[0] = (int) $tokenParts[0];

        // fail to resolve and cast the id, therefore this token is not valid.
        if ($tokenParts[0] === 0) {
            return false;
        }

        return $tokenParts;
    }

    /**
     * Validate if the token is valid(not expired) and exist
     *
     * @param  string  $refreshTokenText
     * @return RefreshToken [on success]
     *
     * @throws SanctumRefreshException if token is invalid [ERR_TOKEN_INVALID_PARSE, ERR_TOKEN_NOT_FOUND, ERR_TOKEN_EXPIRED, ERR_TOKEN_INVALID]
     */
    public static function getRefreshToken(string $refreshTokenText): RefreshToken
    {
        $refreshTokenParts = self::parseRefreshToken($refreshTokenText);

        if (! $refreshTokenParts) {
            throw new SanctumRefreshException(
                '[Invalid Token]: Unable to parse refresh token',
                meta: $refreshTokenText,
                tag: 'ERR_TOKEN_INVALID_PARSE'
            );
        }

        $refreshToken = RefreshToken::find($refreshTokenParts[0]);
        if (! $refreshToken) {
            throw new SanctumRefreshException(
                '[Invalid Token]: Unable to locate refresh token on the Database',
                meta: $refreshTokenParts[0],
                tag: 'ERR_TOKEN_NOT_FOUND'
            );
        }

        if (Carbon::now()->gt($refreshToken->expires_at)) {
            throw new SanctumRefreshException('[Invalid Token]: Token has expired', tag: 'ERR_TOKEN_EXPIRED');
        }

        if ($refreshToken->token !== $refreshTokenParts[1]) {
            throw new SanctumRefreshException('[Invalid Token]: Token is not valid', tag: 'ERR_TOKEN_INVALID');
        }

        return $refreshToken;
    }
}
