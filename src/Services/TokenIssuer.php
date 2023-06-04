<?php

namespace Albet\SanctumRefresh\Services;

use Albet\SanctumRefresh\Exceptions\InvalidTokenException;
use Albet\SanctumRefresh\Exceptions\MustHaveTraitException;
use Albet\SanctumRefresh\Models\RefreshToken;
use Albet\SanctumRefresh\Services\Factories\Token;
use Albet\SanctumRefresh\Services\Factories\TokenConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class TokenIssuer
{
    /**
     * @throws MustHaveTraitException
     */
    public static function issue(Model $tokenable, string $tokenName = 'web', TokenConfig $tokenConfig = new TokenConfig()): Token
    {
        $tokenableTraits = array_values(class_uses($tokenable));

        if (!in_array(HasApiTokens::class, $tokenableTraits)) {
            throw new MustHaveTraitException(get_class($tokenable), HasApiTokens::class);
        }

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

    /**
     * @throws InvalidTokenException
     */
    public static function refreshToken(string $refreshToken, string $tokenName = 'web', TokenConfig $tokenConfig = new TokenConfig()): Token
    {
        if (!str_contains($refreshToken, '|')) {
            throw new InvalidTokenException();
        }

        // Parse the token id
        $tokenId = explode('|', $refreshToken)[0];

        // Find token from given id
        $token = RefreshToken::with('accessToken')
            ->where('expires_at', '>', now())
            ->find($tokenId);

        if (!$token) {
            throw new InvalidTokenException();
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
