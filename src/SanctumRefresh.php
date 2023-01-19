<?php

namespace Albet\SanctumRefresh;

use Albet\SanctumRefresh\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

class SanctumRefresh
{
    static string $authedMessage = "";
    static string $middlewareMsg = "";

    public static function routes($config = []): void
    {
        self::$authedMessage = $config['authedMessage'] ?? "Authentication success!";
        self::$middlewareMsg = $config['middlewareMessage'] ?? "Refresh token is expired or invalid.";

        Route::controller(AuthController::class)->group(function () use ($config) {
            if (!$config['refreshOnly']) {
                Route::post($config['loginUrl'] ?? '/login', 'login')
                    ->middleware($config['loginMiddleware'] ?? null);
            }
            Route::post($config['refreshUrl'] ?? '/refresh', 'refresh')
                ->middleware(array_merge('checkRefreshToken', $config['refreshMiddleware']));
        });
    }
}
