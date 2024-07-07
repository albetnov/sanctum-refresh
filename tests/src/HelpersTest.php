<?php

use Albet\SanctumRefresh\Models\User;
use Albet\SanctumRefresh\Services\TokenIssuer;

use function Albet\SanctumRefresh\checkRefreshToken;

it('can validate refresh token', function () {
    $token = TokenIssuer::issue(User::first());

    expect(checkRefreshToken($token->plainRefreshToken))->toBeTrue();
});

it('return false because token has invalid format', function () {
    $token = 'invalid-token';

    expect(checkRefreshToken($token))->toBeFalse();
});

it('return false because token pos is empty', function () {
    $tokens = ['|', 'a|', '|b'];

    foreach ($tokens as $token) {
        expect(checkRefreshToken($token))->toBeFalse();
    }
});

it('return false because token does not exist', function () {
    $tokens = ['1|a', 'a|b'];

    foreach ($tokens as $token) {
        expect(checkRefreshToken($token))->toBeFalse();
    }
});

it('return false because token is not match with plain text', function () {
    $token = TokenIssuer::issue(User::first());

    $tokenId = $token->refreshToken->id;

    expect(checkRefreshToken("{$tokenId}|invalid-token"))->toBeFalse();
});

it('return false because token is not valid anymore', function () {
    $token = TokenIssuer::issue(User::first());

    $token->refreshToken->update([
        'expires_at' => now()->subDay()
    ]);

    expect(checkRefreshToken($token->plainRefreshToken))->toBeFalse();
});
