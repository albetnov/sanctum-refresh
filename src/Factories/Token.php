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
    public string $plainTextToken;
    public string $plainTextRefreshToken;
    public \DateTime $tokenExpiresAt;
    public \DateTime $refreshTokenExpiresAt;

    public function __construct(
        public NewAccessToken $token,
        string $plainRefreshToken,
        public RefreshToken $refreshToken
    ) {
        $this->plainTextToken = $token->plainTextToken;
        $this->tokenExpiresAt = $this->token->accessToken->expires_at;

        $this->plainTextRefreshToken = "{$this->refreshToken->token_id}|{$plainRefreshToken}";
        $this->refreshTokenExpiresAt = $this->refreshToken->expires_at;
    }

    public function toArray(): array
    {
        return [
            'access_token' => $this->plainTextToken,
            'access_token_expires_at' => $this->tokenExpiresAt->format('Y-m-d H:i:s'),
            'refresh_token' => $this->plainTextRefreshToken,
            'refresh_token_expires_at' => $this->refreshTokenExpiresAt->format('Y-m-d H:i:s'),
        ];
    }
}
