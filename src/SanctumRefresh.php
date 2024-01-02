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
        self::$authedMessage = config('sanctum-refresh.message.authed');
        self::$middlewareMsg = config('sanctum-refresh.message.invalid');
    }

    public static function routes(
        bool $refreshOnly = false,
        string $loginUrl = "/login",
        string|array|null $loginMiddlewares = null,
        string $refreshUrl = "/refresh",
        string|array|null $refreshMiddlewares = null
    ): void {
        Route::controller(AuthController::class)->group(function () use (
            $refreshOnly,
            $loginUrl,
            $loginMiddlewares,
            $refreshUrl,
            $refreshMiddlewares
        ) {
            if (!$refreshOnly) {
                Route::post($loginUrl, 'login')
                    ->name('login')
                    ->middleware($loginMiddlewares);
            }

            Route::post($refreshUrl, 'refresh')
                ->name('refresh')
                ->middleware(
                    array_merge(
                        ['checkRefreshToken'],
                        is_string($refreshMiddlewares) ? [$refreshMiddlewares] : $refreshMiddlewares ?? []
                    )
                );
        });
    }
}
