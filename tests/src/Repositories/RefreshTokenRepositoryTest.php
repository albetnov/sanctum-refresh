<?php

use Albet\SanctumRefresh\Models\PersonalAccessToken;
use Albet\SanctumRefresh\Models\RefreshToken;
use Albet\SanctumRefresh\Models\User;
use Albet\SanctumRefresh\Repositories\RefreshTokenRepository;

it('can revoke token from given valid token id', function () {
    User::first()->createTokenWithRefresh('web');

    $repo = new RefreshTokenRepository();

    expect($repo->revokeRefreshTokenFromTokenId(PersonalAccessToken::first()->id))->toBeTrue();
});

it('cannot revoke token due to invalid token id', function () {
    $repo = new RefreshTokenRepository();

    expect($repo->revokeRefreshTokenFromTokenId(5))->toBeFalse();
});

it('can revoke token from plain token', function () {
    $plain = User::first()->createTokenWithRefresh('web')->plainTextRefreshToken;

    $repo = new RefreshTokenRepository();
    $repo->revokeRefreshTokenFromToken($plain);

    expect(RefreshToken::first())->toBeNull();
});

it('cannot revoke token from plain token (invalid)', function () {
    $repo = new RefreshTokenRepository();

    expect($repo->revokeRefreshTokenFromToken('fake token'))->toBeFalse();
});
