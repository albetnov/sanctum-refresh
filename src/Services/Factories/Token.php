<?php

namespace Albet\SanctumRefresh\Services\Factories;

use Albet\SanctumRefresh\Models\RefreshToken;
use Laravel\Sanctum\NewAccessToken;

readonly class Token
{
    public string $plainRefreshToken;

    public function __construct(
        public NewAccessToken $token,
        string $plainRefreshToken,
        public RefreshToken $refreshToken
    ) {
        $this->plainRefreshToken = "{$this->refreshToken->token_id}|{$plainRefreshToken}";
    }
}
