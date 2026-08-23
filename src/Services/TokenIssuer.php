<?php

namespace Albet\SanctumRefresh\Services;

use Albet\SanctumRefresh\Exceptions\SanctumRefreshException;
use Albet\SanctumRefresh\Factories\Token;
use Albet\SanctumRefresh\Factories\TokenConfig;
use Albet\SanctumRefresh\Helpers;
use Albet\SanctumRefresh\Models\RefreshToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
        ?string $tokenName = null,
        TokenConfig $tokenConfig = new TokenConfig()
    ): Token|false {
        $tokenParts = Helpers::parseRefreshToken($refreshToken);

        if (! $tokenParts) {
            return false;
        }

        // Find token from given access token id (plainTextRefreshToken embeds token_id, not the refresh token's own id)
        $token = RefreshToken::with('accessToken')
            ->check($tokenParts[1])
            ->where('token_id', $tokenParts[0])
            ->first();

        if (! $token) {
            return false;
        }

        return DB::transaction(function () use ($token, $tokenName, $tokenConfig) {
            // Regenerate token, keeping the original token's name unless a new one is given.
            $newToken = $token->accessToken->tokenable
                ->createToken(
                    $tokenName ?? $token->accessToken->name,
                    $tokenConfig->abilities,
                    $tokenConfig->tokenExpireAt
                );

            $plainRefreshToken = Str::random(40);

            $refreshToken = RefreshToken::create([
                'token_id' => $newToken->accessToken->id,
                'token' => hash('sha256', $plainRefreshToken),
                'expires_at' => $tokenConfig->refreshTokenExpireAt,
            ]);

            // Delete child (refresh_tokens) before parent (personal_access_tokens) —
            // token_id is RESTRICT/NO ACTION on MySQL/Postgres.
            $token->delete();
            $token->accessToken->delete();

            return new Token($newToken, $plainRefreshToken, $refreshToken);
        });
    }
}
