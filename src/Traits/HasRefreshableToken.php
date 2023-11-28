<?php

namespace Albet\SanctumRefresh\Traits;

use Albet\SanctumRefresh\Exceptions\MustHaveTraitException;
use Albet\SanctumRefresh\Repositories\RefreshTokenRepository;
use Albet\SanctumRefresh\Services\Factories\Token;
use Albet\SanctumRefresh\Services\Factories\TokenConfig;
use Albet\SanctumRefresh\Services\TokenIssuer;

trait HasRefreshableToken
{
    /**
     * @throws MustHaveTraitException
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
                    $refreshTokenRepository->revokeRefreshTokenFromTokenId($accToken->id);
                    $accToken->delete();

                    return true;
                }
            }
        }

        return false;
    }
}
