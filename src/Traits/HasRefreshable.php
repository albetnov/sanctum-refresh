<?php

namespace Albet\SanctumRefresh\Traits;

use Albet\SanctumRefresh\Models\RefreshToken;

trait HasRefreshable
{
    public function refreshToken()
    {
        return $this->hasOne(RefreshToken::class, 'token_id', 'id');
    }
}
