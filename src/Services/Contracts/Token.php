<?php

namespace Albet\SanctumRefresh\Services\Contracts;

use Albet\SanctumRefresh\Models\RefreshToken;
use Illuminate\Support\Collection;
use Laravel\Sanctum\NewAccessToken;

class Token
{
    const AUTH_INVALID = 'AUTH_INVALID';

    public function __construct(private readonly NewAccessToken $token,
                                private readonly string $plainRefreshToken,
                                private readonly RefreshToken $refreshToken)
    {
    }

    public function getToken(): Collection
    {
        return collect([
            'refreshToken' => $this->refreshToken,
            'accessToken' => $this->token->accessToken,
            'plain' => [
                'accessToken' => $this->token->plainTextToken,
                'refreshToken' => $this->refreshToken->id.'|'.$this->plainRefreshToken,
            ],
        ]);
    }
}
