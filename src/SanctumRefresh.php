<?php

namespace Albet\SanctumRefresh;

use Albet\SanctumRefresh\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

class SanctumRefresh
{
    public static function routes()
    {
        Route::controller(AuthController::class)->group(function () {
            Route::post('/login', 'login');
            Route::post('/refresh', 'refresh')->middleware('checkRefreshToken');
        });
    }
}
