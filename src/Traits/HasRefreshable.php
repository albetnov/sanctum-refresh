<?php

namespace Albet\SanctumRefresh\Traits;

use Albet\SanctumRefresh\Exceptions\InvalidModelException;
use Albet\SanctumRefresh\Models\RefreshToken;
use Illuminate\Database\Eloquent\Relations\HasOne;
use ReflectionClass;

trait HasRefreshable
{
    /**
     * @throws InvalidModelException
     */
    public function refreshToken(): HasOne
    {
        if (! method_exists($this, 'hasOne')) {
            throw new InvalidModelException((new ReflectionClass($this))->name);
        }

        return $this->hasOne(RefreshToken::class, 'token_id', 'id');
    }
}
