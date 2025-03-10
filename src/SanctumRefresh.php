<?php

namespace Albet\SanctumRefresh;

use Albet\SanctumRefresh\Exceptions\SanctumRefreshException;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use ReflectionClass;

class SanctumRefresh
{
    // Use sanctum personal access token model as default.
    public static string $model = PersonalAccessToken::class;

    /**
     * Use custom personal access token model
     * This also alter sanctum personal access token.
     *
     * @throws SanctumRefreshException
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

        throw new SanctumRefreshException(
            "[Runtime Check] Invalid Model: $model. No PersonalAccessToken found",
            meta: [
                'model' => $model,
            ],
            tag: 'ERR_INVALID_MODEL'
        );
    }

    public static function boot(): void {}
}
