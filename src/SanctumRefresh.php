<?php

namespace Albet\SanctumRefresh;

use Albet\SanctumRefresh\Controllers\AuthController;
use Albet\SanctumRefresh\Exceptions\InvalidModelException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use ReflectionClass;

class SanctumRefresh
{
    public static string $authedMessage = '';

    public static string $middlewareMsg = '';

    // Use sanctum personal access token model as default.
    public static string $model = PersonalAccessToken::class;

    /**
     * Use custom personal access token model
     * This also alter sanctum personal access token.
     *
     * @throws InvalidModelException
     */
    public static function usePersonalAccessTokenModel(string|callable $model): void
    {
        if (class_exists($model)) {
            $checkModel = new ReflectionClass($model);
            if (
                $checkModel->getParentClass() !== false &&
                ($checkModel->getParentClass()->name === Model::class ||
                $checkModel->getParentClass()->name === PersonalAccessToken::class ||
                $checkModel->getParentClass()->name === \Albet\SanctumRefresh\Models\PersonalAccessToken::class)
            ) {
                Sanctum::usePersonalAccessTokenModel($model);
                self::$model = $model;

                return;
            }
        }

        throw new InvalidModelException($model);
    }

    public static function boot(): void
    {
        self::$authedMessage = config('sanctum-refresh.sanctum_refresh.message.authed');
        self::$middlewareMsg = config('sanctum-refresh.sanctum_refresh.message.invalidMsg');
    }

    public static function routes(): void
    {
        Route::controller(AuthController::class)->group(function () {
            if (! config('sanctum-refresh.sanctum_refresh.routes.refreshOnly')) {
                Route::post(config('sanctum-refresh.sanctum_refresh.routes.urls.login'), 'login')
                    ->name('login')
                    ->middleware(config('sanctum-refresh.sanctum_refresh.routes.middlewares.login'));
            }

            Route::post(config('sanctum-refresh.sanctum_refresh.routes.urls.refresh'), 'refresh')
                ->name('refresh')
                ->middleware(config('sanctum-refresh.sanctum_refresh.routes.middlewares.refresh'));
        });
    }
}
