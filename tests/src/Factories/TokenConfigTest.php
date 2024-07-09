<?php

use Albet\SanctumRefresh\Factories\TokenConfig;
use Carbon\Carbon;
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
        ->and($tokenConfig->tokenExpireAt)->toBeCarbon(Carbon::now()->addMinutes(5)->toDateTimeString());

    expect($tokenConfig->refreshTokenExpireAt)->toBeCarbon(Carbon::now()->addMinutes(10)->toDateTimeString());
});

function getExpireMethod(...$args): ?Carbon
{
    $getExpire = new \ReflectionMethod(TokenConfig::class, 'getExpire');
    $getExpire->setAccessible(true);

    $expire = $getExpire->invoke(new TokenConfig(), ...$args);

    return $expire;
}

it('get expires from argument correctly', function () {
    testTime()->freeze();
    $expire = getExpireMethod('example', Carbon::now());

    expect($expire)->toBeInstanceOf(Carbon::class)
        ->toBeCarbon(Carbon::now()->toDateTimeString());
});

it('get expires from config correctly', function () {
    $duration = 10;

    Config::set('sanctum-refresh.expiration.example', $duration);

    testTime()->freeze();

    $expire = getExpireMethod('example', null);

    expect($expire)->toBeInstanceOf(Carbon::class)
        ->toBeCarbon(Carbon::now()->addMinute($duration)->toDateTimeString());
});

it('get expires return null when no config specified and no overrides', function () {
    expect(getExpireMethod('example', null))->toBeNull();
});
