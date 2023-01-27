<?php

use Albet\SanctumRefresh\Exceptions\InvalidTokenException;
use Albet\SanctumRefresh\Exceptions\MustExtendHasApiTokens;
use Albet\SanctumRefresh\Models\RefreshToken;
use Albet\SanctumRefresh\Models\User;
use Albet\SanctumRefresh\SanctumRefresh;
use Albet\SanctumRefresh\Services\Contracts\Token;
use Albet\SanctumRefresh\Services\TokenIssuer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    SanctumRefresh::usePersonalAccessTokenModel(\Albet\SanctumRefresh\Models\PersonalAccessToken::class);
});

function tokenKeys(): array
{
    return ['accessToken', 'refreshToken', 'plain'];
}

it('return auth invalid if bool false passed', function () {
    expect(TokenIssuer::issue(false))->toBe(Token::AUTH_INVALID);
});

it('throws MustExtendHasApiTokens if not trait found in the given model', function () {
    $fakeModel = new class extends Model
    {
    };

    expect(TokenIssuer::issue($fakeModel))->toThrow(MustExtendHasApiTokens::class);
})->throws(MustExtendHasApiTokens::class);

it('successfully create a token', function () {
    expect(TokenIssuer::issue(User::first())->toArray())->toHaveKeys(tokenKeys());
});

it('throw invalid token when no indicator given', function () {
    expect(TokenIssuer::refreshToken('fake refresh'))->toThrow(InvalidTokenException::class);
})->throws(InvalidTokenException::class);

it('throw invalid token when given id is invalid', function () {
    expect(TokenIssuer::refreshToken('1|token'))->toThrow(InvalidTokenException::class);
})->throws(InvalidTokenException::class);

it('generate the refresh token successfully', function () {
    $refreshToken = TokenIssuer::issue(User::find(1))->toArray()['plain']['refreshToken'];

    expect(TokenIssuer::refreshToken($refreshToken)->toArray())->toHaveKeys(tokenKeys());
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
