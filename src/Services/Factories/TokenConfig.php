<?php

namespace Albet\SanctumRefresh\Services\Factories;

use Carbon\Carbon;

readonly class TokenConfig
{
    public Carbon $tokenExpireAt;

    public Carbon $refreshTokenExpireAt;

    public function __construct(public array $abilities = ['*'], ?Carbon $tokenExpireAt = null, ?Carbon $refreshTokenExpireAt = null)
    {
        $this->tokenExpireAt = $this->getDefaultToken($tokenExpireAt);
        $this->refreshTokenExpireAt = $this->getDefaultRefreshToken($refreshTokenExpireAt);
    }

    private function getToken(?Carbon $token, string $configString): ?Carbon
    {
        if (! $token) {
            $config = config($configString);
            if (! $config) {
                return null;
            }

            return now()->addMinutes($config);
        }

        return $token;
    }

    /**
     * @throws \Exception
     */
    private function getDefaultToken(?Carbon $token): Carbon
    {
        $result = self::getToken($token, 'sanctum-refresh.expiration.access_token');

        if (! $result) {
            throw new \Exception('sanctum-refresh.expiration.access_token is not set');
        }

        return $result;
    }

    private function getDefaultRefreshToken(?Carbon $token): Carbon
    {
        return self::getToken($token, 'sanctum-refresh.expiration.refresh_token');
    }
}
