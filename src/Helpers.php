<?php

namespace Albet\SanctumRefresh;

use Albet\SanctumRefresh\Exceptions\InvalidTokenException;
use Albet\SanctumRefresh\Exceptions\SanctumRefreshException;
use Albet\SanctumRefresh\Exceptions\TokenExpiredException;
use Albet\SanctumRefresh\Exceptions\TokenNotFoundException;
use Albet\SanctumRefresh\Models\RefreshToken;
use Carbon\Carbon;

class Helpers
{
    const TOKEN_SEPERATOR = '|';

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

        // we fail to resolve and cast the id, therefore this token is not valid.
        if ($tokenParts[0] === 0) {
            return false;
        }

        return $tokenParts;
    }

    /**
     * Validate if the token is valid(not expired) and exist
     *
     * @param  array|string  $refreshToken  [Can be parts (array) or a plain refresh token (string)]
     * @return RefreshToken [on success]
     *
     * @throws InvalidTokenException if token is invalid
     * @throws TokenExpiredException if token is expired
     * @throws TokenNotFoundException if token is not found
     */
    public static function validateRefreshToken(array|string $refreshToken): RefreshToken
    {
        if (is_string($refreshToken)) {
            $refreshToken = self::parseRefreshToken($refreshToken);

            if (! $refreshToken) {
                throw new InvalidTokenException();
            }
        }

        if (count($refreshToken) < 2) {
            throw new InvalidTokenException();
        }

        $refreshToken = RefreshToken::find($refreshToken[0]);
        if (! $refreshToken) {
            throw new TokenNotFoundException();
        }

        if (Carbon::now()->gt($refreshToken->expires_at)) {
            throw new TokenExpiredException();
        }

        return $refreshToken;
    }

    /**
     * Verify if the refresh token valid (not expired) and exist.
     * A convenient wrapper around `validateRefreshToken` to return bool instead of throw exceptions.
     *
     * @param  array|string  $refreshToken  [Can be parts (array) or a plain refresh token (string)]
     * @return RefreshToken|false [indicates validity of the token]
     */
    public static function verifyRefreshToken(array|string $refreshToken): RefreshToken|false
    {
        try {
            return self::validateRefreshToken($refreshToken);
        } catch (SanctumRefreshException $e) {
            return false;
        }
    }
}
