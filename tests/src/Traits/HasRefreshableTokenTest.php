<?php

use Albet\SanctumRefresh\Factories\Token;
use Albet\SanctumRefresh\Models\RefreshToken;
use Albet\SanctumRefresh\Models\User;

it('create token with refresh successfully', function () {
    expect(User::first()->createTokenWithRefresh('web'))->toBeInstanceOf(Token::class)
        ->and(RefreshToken::first())->not->toBeNull();
});

it('revoked both token successfully', function () {
    // Create the token
    User::first()->createTokenWithRefresh('web');

    // Revoke it
    expect(User::first()->revokeBothToken())->toBeTrue()
        ->and(User::first()->tokens->isEmpty())->toBeTrue() // ensure the relationship null as has been deleted
        ->and(RefreshToken::first())->toBeNull(); // make sure refresh token entry is empty
});

it('not revoked any token because user not have one', function () {
    expect(User::first()->revokeBothToken())->toBeFalse();
});
