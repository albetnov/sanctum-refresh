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

it('validate and return correct refresh token instance', function () {
    // Desync refresh_tokens.id from token_id so a naive find($id) would miss.
    RefreshToken::create(['token' => Str::random(64), 'expires_at' => now()->addMinutes(10), 'token_id' => 999]);

    $plain = Str::random(40);
    $refreshToken = RefreshToken::create([
        'token' => hash('sha256', $plain),
        'expires_at' => now()->addMinutes(10),
        'token_id' => 1,
    ]);

    $fetchedToken = Helpers::getRefreshToken('1|' . $plain);

    expect($fetchedToken)->toBeInstanceOf(RefreshToken::class)
        ->and($fetchedToken->id)->toBe($refreshToken->id);
});

it('fail validate and throw invalid token exception (string)', function () {
    Helpers::getRefreshToken('test');
})->throws(SanctumRefreshException::class, '[Invalid Token]: Unable to parse refresh token');

it('fail validate and throw token not found exception', function () {
    Helpers::getRefreshToken('1|abc');
})->throws(SanctumRefreshException::class, '[Invalid Token]: Unable to locate refresh token on the Database');

it('fail validate and throw token expired exception', function () {
    RefreshToken::create([
        'token' => 'abc',
        'expires_at' => now()->subMinutes(10),
        'token_id' => 1,
    ]);

    Helpers::getRefreshToken('1|abc');
})->throws(SanctumRefreshException::class, '[Invalid Token]: Token has expired');
