<?php

namespace Albet\SanctumRefresh\Traits;

use Albet\SanctumRefresh\Exceptions\SanctumRefreshException;
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
            throw new SanctumRefreshException(
                "[With Refreshable]: Model is not valid",
                meta: ['model' => (new ReflectionClass($this))->name],
                tag: 'ERR_INVALID_MODEL'
            );
        }

        return $this->hasOne(RefreshToken::class, 'token_id', 'id');
    }
}
