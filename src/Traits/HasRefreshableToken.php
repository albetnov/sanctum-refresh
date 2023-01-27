<?php

namespace Albet\SanctumRefresh\Traits;

use Albet\SanctumRefresh\Exceptions\InvalidTokenException;
use Albet\SanctumRefresh\Exceptions\MustExtendHasApiTokens;
use Albet\SanctumRefresh\Repositories\RefreshTokenRepository;
use Albet\SanctumRefresh\Services\TokenIssuer;
use Carbon\Carbon;
use Illuminate\Support\Collection;

trait HasRefreshableToken
{

    /**
     * @throws MustExtendHasApiTokens
     */
    public function createTokenWithRefresh(string $name, array $config = []): Collection
    {
        return TokenIssuer::issue($this, $name, $config);
    }

    public function revokeBothToken(): bool
    {
        $accTokens = $this->load('tokens')->tokens->all();

        if ($accTokens) {
            foreach ($accTokens as $accToken) {
                if ($accToken->id) {
                    (new RefreshTokenRepository())->revokeRefreshTokenFromTokenId($accToken->id);
                    $accToken->delete();
                    return true;
                }
            }
        }

        return false;
    }
}
