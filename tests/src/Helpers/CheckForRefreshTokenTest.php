<?php

use Albet\SanctumRefresh\Exceptions\InvalidTokenException;
use Albet\SanctumRefresh\Helpers\CheckForRefreshToken;
use Albet\SanctumRefresh\Models\User;
use Albet\SanctumRefresh\Services\TokenIssuer;
use Illuminate\Support\Str;

it('verifies that the refresh token given is valid', function () {
    $token = TokenIssuer::issue(User::first());

    $refreshToken = $token->get('plain')['refreshToken'];

    expect(CheckForRefreshToken::check($refreshToken))->toBeTrue();
});

it('verifies that the refresh token given is invalid', function () {
    $refreshToken = 'random | string';

    expect(CheckForRefreshToken::check($refreshToken))->toThrow(InvalidTokenException::class);
})->throws(InvalidTokenException::class);

it('verifies that the refresh token given is invalid (no indicator)', function () {
    $refreshToken = 'random string';

    expect(CheckForRefreshToken::check($refreshToken))->toThrow(InvalidTokenException::class);
})->throws(InvalidTokenException::class);

it('verifies that the token is invalid even id is correct', function () {
    $token = TokenIssuer::issue(User::first());

    $refreshTokenId = $token->get('refreshToken')->id;
    $refreshToken = $refreshTokenId.'|'.Str::random(40);

    expect(CheckForRefreshToken::check($refreshToken))->toThrow(InvalidTokenException::class);
})->throws(InvalidTokenException::class);
