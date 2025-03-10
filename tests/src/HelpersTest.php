<?php

use Albet\SanctumRefresh\Exceptions\SanctumRefreshException;
use Albet\SanctumRefresh\Helpers;
use Albet\SanctumRefresh\Models\RefreshToken;
use Illuminate\Support\Str;

uses()->group('helpers');

it('can parse refresh token successfully', function () {
    $tokenParts = Helpers::parseRefreshToken('1|abc');

    expect($tokenParts)->toBeArray()
        ->and($tokenParts[0])->toBe(1)
        ->and($tokenParts[1])->toBe('abc');
});

it('cannot parse refresh token due to invalid format (contains seperator)', function () {
    expect(Helpers::parseRefreshToken('abc'))->toBeFalsy();
});

it('cannot parse refresh token due to invalid format (split < 2)', function () {
    expect(Helpers::parseRefreshToken('|abc'))->toBeFalsy()
        ->and(Helpers::parseRefreshToken('abc|'))->toBeFalsy();
});

it('validate and return correct refresh token instance (string)', function () {
    $refreshToken = RefreshToken::create([
        'token' => $plain = Str::random(40),
        'expires_at' => now()->addMinutes(10),
        'token_id' => 1,
    ]);

    $fetchedToken = Helpers::validateRefreshToken('1|' . $plain);

    expect($fetchedToken)->toBeInstanceOf(RefreshToken::class)
        ->and($fetchedToken->id)->toBe($refreshToken->id);
});

it('validate and return correct refresh token instance (array)', function () {
    $refreshToken = RefreshToken::create([
        'token' => 'abc',
        'expires_at' => now()->addMinutes(10),
        'token_id' => 1,
    ]);

    $fetchedToken = Helpers::validateRefreshToken([$refreshToken->id, $refreshToken->token]);

    expect($fetchedToken)->toBeInstanceOf(RefreshToken::class)
        ->and($fetchedToken->id)->toBe($refreshToken->id);
});

it('fail validate and throw invalid token exception (string)', function () {
    Helpers::validateRefreshToken('test');
})->throws(SanctumRefreshException::class, '[Invalid Token]: Unable to parse refresh token');

it('fail validate and throw invalid token exception (array)', function () {
    Helpers::validateRefreshToken([1]);
})->throws(SanctumRefreshException::class, '[Invalid Token]: Expected array to contains 2 parts. Got < 2 instead');

it('fail validate and throw token not found exception', function () {
    Helpers::validateRefreshToken('1|abc');
})->throws(SanctumRefreshException::class, '[Invalid Token]: Unable to locate refresh token on the Database');

it('fail validate and throw token expired exception', function () {
    RefreshToken::create([
        'token' => 'abc',
        'expires_at' => now()->subMinutes(10),
        'token_id' => 1,
    ]);

    Helpers::validateRefreshToken('1|abc');
})->throws(SanctumRefreshException::class, '[Invalid Token]: Token has expired');
