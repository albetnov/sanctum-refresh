<?php

namespace Albet\SanctumRefresh\Traits;

use Albet\SanctumRefresh\Exceptions\SanctumRefreshException;
use Albet\SanctumRefresh\Factories\Token;
use Albet\SanctumRefresh\Factories\TokenConfig;
use Albet\SanctumRefresh\Repositories\RefreshTokenRepository;
use Albet\SanctumRefresh\Services\TokenIssuer;

trait HasRefreshableToken
{
    /**
     * @throws SanctumRefreshException[ERR_INVALID_MODEL]
     */
    public function createTokenWithRefresh(string $name, TokenConfig $tokenConfig = new TokenConfig()): Token
    {
        return TokenIssuer::issue($this, $name, $tokenConfig);
    }

    public function revokeBothToken(): bool
    {
        $accTokens = $this->load('tokens')->tokens->all();

        if ($accTokens) {
            /** @var RefreshTokenRepository $refreshTokenRepository */
            $refreshTokenRepository = app(RefreshTokenRepository::class);
            foreach ($accTokens as $accToken) {
                // @phpstan-ignore-next-line
                if ($accToken->id) {
                    $refreshTokenRepository->revokeFromTokenId($accToken->id);
                    $accToken->delete();

                    return true;
                }
            }
        }

        return false;
    }
}
