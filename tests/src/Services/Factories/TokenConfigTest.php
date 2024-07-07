<?php

use Albet\SanctumRefresh\Services\Factories\TokenConfig;
use Illuminate\Support\Facades\Config;
use function Spatie\PestPluginTestTime\testTime;

it('token config factory built successfully', function () {
    $tokenExpireAt = now()->addMinutes(5);
    $refreshTokenExpireAt = now()->addMinutes(10);

    $tokenConfig = new TokenConfig(
        abilities: ['*'],
        tokenExpireAt: $tokenExpireAt,
        refreshTokenExpireAt: $refreshTokenExpireAt
    );

    expect($tokenConfig->abilities)->toBeArray()->toHaveKey(0, '*')
        ->and($tokenConfig->tokenExpireAt)->toBe($tokenExpireAt)
        ->and($tokenConfig->refreshTokenExpireAt)->toBe($refreshTokenExpireAt);
});

it('token config factory built successfully (default values)', function () {
    Config::set('sanctum-refresh.expiration.access_token', 5);
    Config::set('sanctum-refresh.expiration.refresh_token', 10);
    testTime()->freeze();
    $tokenConfig = new TokenConfig();

    expect($tokenConfig->abilities)->toBeArray()->toHaveKey(0, '*')
        ->and($tokenConfig->tokenExpireAt)->toBeCarbon(now()->addMinutes(5)->toDateTimeString());

    expect($tokenConfig->refreshTokenExpireAt)->toBeCarbon(now()->addMinutes(10)->toDateTimeString());
});

it('complained when config of access token expiration is null', function () {
    Config::set('sanctum-refresh.expiration.access_token', null);

    $tokenConfig = new TokenConfig();
    expect($tokenConfig)->toThrow(\Exception::class);
})->throws(\Exception::class);
