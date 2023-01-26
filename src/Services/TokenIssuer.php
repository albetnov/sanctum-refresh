<?php

namespace Albet\SanctumRefresh\Services;

use Albet\SanctumRefresh\Exceptions\InvalidTokenException;
use Albet\SanctumRefresh\Exceptions\MustExtendHasApiTokens;
use Albet\SanctumRefresh\Models\RefreshToken;
use Albet\SanctumRefresh\Services\Contracts\Token;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TokenIssuer
{
    /**
     * @throws MustExtendHasApiTokens
     */
    public static function issue(Model|bool $user, string $tokenName = 'web', array $config = []): Collection|string
    {
        if (! $user) {
            return Token::AUTH_INVALID;
        }

        if (! method_exists($user, 'createToken')) {
            throw new MustExtendHasApiTokens(get_class($user));
        }

        $token = $user->createToken($tokenName,
            $config['abilities'] ?? ['*'],
            $config['token_expires_at'] ??
            now()->addMinutes(config('sanctum-refresh.expiration.access_token')));

        $plainRefreshToken = Str::random(40);

        $refreshToken = RefreshToken::create([
            'token' => hash('sha256', $plainRefreshToken),
            'expires_at' => $config['refresh_expires_at'] ??
                now()->addMinutes(config('sanctum-refresh.expiration.refresh_token')),
            'token_id' => $token->accessToken->id,
        ]);

        return (new Token($token, $plainRefreshToken, $refreshToken))->getToken();
    }

    /**
     * @throws InvalidTokenException
     */
    public static function refreshToken(string $refreshToken, string $tokenName = 'web', array $config = []): Collection|string
    {
        if (! str_contains($refreshToken, '|')) {
            throw new InvalidTokenException();
        }

        // Parse the token id
        $tokenId = explode('|', $refreshToken)[0];

        // Find token from given id
        $token = RefreshToken::with('accessToken')->find($tokenId);

        if (! $token) {
            throw new InvalidTokenException();
        }

        // Regenerate token.
        $newToken = $token->accessToken->tokenable
            ->createToken($tokenName,
                $config['abilities'] ?? ['*'],
                $config['token_expires_at'] ??
                now()->addMinutes(config('sanctum-refresh.expiration.access_token')));

        $plainRefreshToken = Str::random(40);

        $refreshToken = RefreshToken::create([
            'token_id' => $newToken->accessToken->id,
            'token' => hash('sha256', $plainRefreshToken),
            'expires_at' => $config['refresh_expires_at'] ??
                now()->addMinutes(config('sanctum-refresh.expiration.refresh_token')),
        ]);

        // Delete current token (revoke refresh token)
        $token->accessToken->delete();
        $token->delete();

        return (new Token($newToken, $plainRefreshToken, $refreshToken))->getToken();
    }
}
