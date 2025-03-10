<?php

use Albet\SanctumRefresh\Models\PersonalAccessToken;
use Albet\SanctumRefresh\Models\RefreshToken;
use Albet\SanctumRefresh\Models\User;
use Albet\SanctumRefresh\Repositories\RefreshTokenRepository;

it('can revoke token from given valid token id', function () {
    User::first()->createTokenWithRefresh('web');

    $repo = new RefreshTokenRepository();

    expect($repo->revokeFromTokenId(PersonalAccessToken::first()->id))->toBeTrue();
});

it('cannot revoke token due to invalid token id', function () {
    $repo = new RefreshTokenRepository();

    expect($repo->revokeFromTokenId(5))->toBeFalse();
});

it('can revoke token from plain token', function () {
    $token = User::first()->createTokenWithRefresh('web');

    $repo = new RefreshTokenRepository();
    expect($repo->revokeFromTokenText($token->plainTextRefreshToken))->toBeTrue();

    expect(RefreshToken::find($token->refreshToken->id))->toBeNull();
});

it('cannot revoke token from plain token (invalid)', function () {
    $repo = new RefreshTokenRepository();

    expect($repo->revokeFromTokenText('fake token'))->toBeFalse();
});
