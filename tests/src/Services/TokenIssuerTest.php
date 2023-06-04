<?php

use Albet\SanctumRefresh\Exceptions\InvalidTokenException;
use Albet\SanctumRefresh\Exceptions\MustHaveTraitException;
use Albet\SanctumRefresh\Models\RefreshToken;
use Albet\SanctumRefresh\Models\User;
use Albet\SanctumRefresh\Services\Factories\Token;
use Albet\SanctumRefresh\Services\TokenIssuer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('throws MustExtendHasApiTokens if not trait found in the given model', function () {
    $fakeModel = new class extends Model
    {
    };

    expect(TokenIssuer::issue($fakeModel))->toThrow(MustHaveTraitException::class);
})->throws(MustHaveTraitException::class);

it('successfully create a token', function () {
    expect(TokenIssuer::issue(User::first()))->toBeInstanceOf(Token::class);
});

it('throw invalid token when no indicator given', function () {
    expect(TokenIssuer::refreshToken('fake refresh'))->toThrow(InvalidTokenException::class);
})->throws(InvalidTokenException::class);

it('throw invalid token when given id is invalid', function () {
    expect(TokenIssuer::refreshToken('1|token'))->toThrow(InvalidTokenException::class);
})->throws(InvalidTokenException::class);

it('generate the refresh token successfully', function () {
    $refreshToken = TokenIssuer::issue(User::find(1))->plainRefreshToken;

    expect(TokenIssuer::refreshToken($refreshToken))->toBeInstanceOf(Token::class);
});

it('throw invalid token when token already expired', function () {
    $fakeToken = Str::random(40);
    $id = RefreshToken::create([
        'token_id' => 1,
        'token' => $fakeToken,
        'expires_at' => now()->subMinutes(30),
    ])->id;

    $fakeTokenable = $id.'|'.$fakeToken;

    expect(TokenIssuer::refreshToken($fakeTokenable))->toThrow(InvalidTokenException::class);
})->throws(InvalidTokenException::class);
