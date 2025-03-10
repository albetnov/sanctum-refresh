<?php

namespace Albet\SanctumRefresh\Factories;

use Albet\SanctumRefresh\Models\RefreshToken;
use Laravel\Sanctum\NewAccessToken;

/**
 * A data class representing Token which also contains
 * RefreshToken
 */
readonly class Token
{
    public string $plainTextRefreshToken;

    public function __construct(
        public NewAccessToken $token,
        string $plainTextToken,
        public RefreshToken $refreshToken
    ) {
        $this->plainTextRefreshToken = "{$this->refreshToken->token_id}|{$plainTextToken}";
    }
}
