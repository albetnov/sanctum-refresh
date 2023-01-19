<?php

namespace Albet\SanctumRefresh;

use Albet\SanctumRefresh\Controllers\AuthController;
use Albet\SanctumRefresh\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;

class SanctumRefresh
{
    public static function routes()
    {
        Route::controller(AuthController::class)->group(function() {
            Route::post('/login', 'login');
            Route::post('/refresh', 'refresh')->middleware('checkRefreshToken');
        });
    }

    public static function configure()
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
