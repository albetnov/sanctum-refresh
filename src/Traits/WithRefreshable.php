<?php

namespace Albet\SanctumRefresh\Traits;

use Albet\SanctumRefresh\Exceptions\InvalidModelException;
use Albet\SanctumRefresh\Models\RefreshToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use ReflectionClass;

trait WithRefreshable
{
    /**
     * @throws InvalidModelException
     */
    public function refreshToken(): HasOne
    {
        if (! in_array(Model::class, class_parents($this))) {
            throw new InvalidModelException((new ReflectionClass($this))->name);
        }

        return $this->hasOne(RefreshToken::class, 'token_id', 'id');
    }
}
