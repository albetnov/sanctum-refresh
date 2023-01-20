<?php

namespace Albet\SanctumRefresh;

use Albet\SanctumRefresh\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

class SanctumRefresh
{
    public static string $authedMessage = '';

    public static string $middlewareMsg = '';

    public static function routes($config = []): void
    {
        self::$authedMessage = $config['authedMessage'] ?? 'Authentication success!';
        self::$middlewareMsg = $config['middlewareMessage'] ?? 'Refresh token is expired or invalid.';

        Route::controller(AuthController::class)->group(function () use ($config) {
            if (! isset($config['refreshOnly'])) {
                Route::post($config['loginUrl'] ?? '/login', 'login')
                    ->name('login')
                    ->middleware($config['loginMiddleware'] ?? null);
            }

            $refreshMiddleware = ['checkRefreshToken'];

            if ($config['refreshMiddleware'] ?? false) {
                if (is_string($config['refreshMiddleware'])) {
                    $refreshMiddleware[] = $config['refreshMiddleware'];
                } else {
                    $refreshMiddleware = array_merge($refreshMiddleware, $config['refreshMiddleware']);
                }
            }

            Route::post($config['refreshUrl'] ?? '/refresh', 'refresh')
                ->name('refresh')
                ->middleware($refreshMiddleware);
        });
    }
}
