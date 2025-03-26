<?php

namespace Albet\SanctumRefresh\Services;

use Albet\SanctumRefresh\Exceptions\SanctumRefreshException;
use Albet\SanctumRefresh\Factories\Token;
use Albet\SanctumRefresh\Factories\TokenConfig;
use Albet\SanctumRefresh\Helpers;
use Albet\SanctumRefresh\Models\RefreshToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class TokenIssuer
{
    /**
     * @throws SanctumRefreshException [ERR_INVALID_MODEL]
     */
    public static function issue(
        Model $tokenable,
        string $tokenName = 'web',
        TokenConfig $tokenConfig = new TokenConfig()
    ): Token {
        $tokenableTraits = array_values(class_uses($tokenable));

        if (! in_array(HasApiTokens::class, $tokenableTraits)) {
            throw new SanctumRefreshException(
                "[Issue Token]: Model is not valid",
                meta: ['model' => get_class($tokenable)],
                tag: 'ERR_INVALID_MODEL'
            );
        }

        /* @phpstan-ignore-next-line */
        $token = $tokenable->createToken(
            $tokenName,
            $tokenConfig->abilities,
            $tokenConfig->tokenExpireAt
        );

        $plainRefreshToken = Str::random(40);

        $refreshToken = RefreshToken::create([
            'token' => hash('sha256', $plainRefreshToken),
            'expires_at' => $tokenConfig->refreshTokenExpireAt,
            'token_id' => $token->accessToken->id,
        ]);

        return new Token($token, $plainRefreshToken, $refreshToken);
    }

    public static function refreshToken(
        string $refreshToken,
        string $tokenName = 'web',
        TokenConfig $tokenConfig = new TokenConfig()
    ): Token|false {
        $tokenParts = Helpers::parseRefreshToken($refreshToken);

        if (! $tokenParts) {
            return false;
        }

        // Find token from given id
        $token = RefreshToken::with('accessToken')
            ->check($tokenParts[1])
            ->find($tokenParts[0]);

        if (! $token) {
            return false;
        }

        // Regenerate token.
        $newToken = $token->accessToken->tokenable
            ->createToken(
                $tokenName,
                $tokenConfig->abilities,
                $tokenConfig->tokenExpireAt
            );

        $plainRefreshToken = Str::random(40);

        $refreshToken = RefreshToken::create([
            'token_id' => $newToken->accessToken->id,
            'token' => hash('sha256', $plainRefreshToken),
            'expires_at' => $tokenConfig->refreshTokenExpireAt,
        ]);

        // Delete current token (revoke refresh token)
        $token->accessToken->delete();
        $token->delete();

        return new Token($newToken, $plainRefreshToken, $refreshToken);
    }
}
