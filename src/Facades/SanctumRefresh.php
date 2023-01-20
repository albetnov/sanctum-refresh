<?php

namespace Albet\SanctumRefresh\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Albet\SanctumRefresh\SanctumRefresh
 */
class SanctumRefresh extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Albet\SanctumRefresh\SanctumRefresh::class;
    }
}
