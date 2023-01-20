<?php

namespace Albet\SanctumRefresh\Tests;

use Albet\SanctumRefresh\Middleware\CheckRefreshToken;

class Kernel extends \Illuminate\Foundation\Http\Kernel
{
    protected $routeMiddleware = [
        'checkRefreshToken' => CheckRefreshToken::class,
    ];
}
