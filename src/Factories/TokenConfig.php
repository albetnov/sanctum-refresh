<?php

namespace Albet\SanctumRefresh\Factories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

readonly class TokenConfig
{
    public ?Carbon $tokenExpireAt;

    public ?Carbon $refreshTokenExpireAt;

    public function __construct(public array $abilities = ['*'], ?Carbon $tokenExpireAt = null, ?Carbon $refreshTokenExpireAt = null)
    {
        $this->tokenExpireAt = $this->getExpire('access_token', $tokenExpireAt);
        $this->refreshTokenExpireAt = $this->getExpire('refresh_token', $refreshTokenExpireAt);
    }

    private function getExpire(string $configKey, ?Carbon $default): ?Carbon
    {
        if ($default) {
            return $default;
        }

        $expire = Config::get("sanctum-refresh.expiration.$configKey");

        if (! $expire) {
            return null;
        }

        return now()->addMinutes($expire);
    }
}
