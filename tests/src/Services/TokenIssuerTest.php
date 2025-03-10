<?php

use Albet\SanctumRefresh\Exceptions\SanctumRefreshException;
use Albet\SanctumRefresh\Factories\Token;
use Albet\SanctumRefresh\Models\RefreshToken;
use Albet\SanctumRefresh\Models\User;
use Albet\SanctumRefresh\Services\TokenIssuer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

it('throws ERR_INVALID_MODEL if tokenable model not having HasApiToken trait', function () {
    $fakeModel = new class extends Model {};

    expect(fn() => TokenIssuer::issue($fakeModel))
        ->toThrow(SanctumRefreshException::class, '[Issue Token]: Model is not valid');
});

it('successfully create a token', function () {
    expect(TokenIssuer::issue(User::first()))->toBeInstanceOf(Token::class);
});

it('throw invalid token when no indicator given', function () {
    expect(TokenIssuer::refreshToken('fake refresh'))->toBeFalse();
});

it('throw invalid token when given id is invalid', function () {
    expect(TokenIssuer::refreshToken('1|token'))->toBeFalse();
});

it('generate the refresh token successfully', function () {
    $refreshToken = TokenIssuer::issue(User::find(1))->plainTextRefreshToken;

    expect(TokenIssuer::refreshToken($refreshToken))->toBeInstanceOf(Token::class);
});

it('throw invalid token when token already expired', function () {
    $fakeToken = Str::random(40);
    $id = RefreshToken::create([
        'token_id' => 1,
        'token' => $fakeToken,
        'expires_at' => now()->subMinutes(30),
    ])->id;

    $fakeTokenable = $id . '|' . $fakeToken;

    expect(TokenIssuer::refreshToken($fakeTokenable))->toBeFalse();
});
