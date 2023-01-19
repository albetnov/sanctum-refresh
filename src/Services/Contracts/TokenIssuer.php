<?php

namespace Albet\SanctumRefresh\Services\Contracts;

use Albet\SanctumRefresh\Helpers\Calculate;
use Illuminate\Support\Collection;
use Laravel\Sanctum\NewAccessToken;

class TokenIssuer
{
    const AUTH_INVALID = 'AUTH_INVALID';

    public function __construct(private NewAccessToken $token)
    {
    }

    public function getToken(): Collection
    {
        return collect([
            'token' => $this->token->plainTextToken,
            'expires_in' => $this->token->accessToken->expires_at,
            'refresh_token' => $this->token->accessToken->plain_refresh_token,
            'refresh_token_expires_in' => Calculate::estimateRefreshToken($this->token->accessToken->created_at),
            'cookie' => cookie(
                'refresh_token',
                $this->token->accessToken->plain_refresh_token,
                0,
                null,
                null,
                null,
                true
            ),
        ]);
    }
}
