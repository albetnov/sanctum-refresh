<?php

use Albet\SanctumRefresh\Factories\Token;
use Albet\SanctumRefresh\Models\RefreshToken;
use Albet\SanctumRefresh\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;

use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    testTime()->freeze();
    $this->expiresAt = now()->addMinutes(Config::get('sanctum-refresh.expiration.access_token'));

    $token = User::find(1)->createToken(
        'web',
        ['*'],
        $this->expiresAt,
    );

    $plainRefreshToken = Str::random(40);

    $this->refreshExpiresAt = now()->addMinutes(Config::get('sanctum-refresh.expiration.refresh_token'));

    $refreshToken = RefreshToken::create([
        'token' => hash('sha256', $plainRefreshToken),
        'expires_at' => $this->refreshExpiresAt,
        'token_id' => $token->accessToken->id,
    ]);

    $this->tokenInstance = new Token($token, $plainRefreshToken, $refreshToken);
});

it('token factory built successfully', function () {
    expect($this->tokenInstance->token)->toBeInstanceOf(NewAccessToken::class)
        ->and($this->tokenInstance->plainTextToken)->toBeString()
        ->and($this->tokenInstance->plainTextRefreshToken)->toBeString()
        ->and($this->tokenInstance->refreshToken)->toBeInstanceOf(RefreshToken::class)
        ->and($this->tokenInstance->tokenExpiresAt->format('Y-m-d H:i:s'))->toBe($this->expiresAt->format('Y-m-d H:i:s'))
        ->and($this->tokenInstance->refreshTokenExpiresAt->format('Y-m-d H:i:s'))->toBe($this->refreshExpiresAt->format('Y-m-d H:i:s'));
});

it('token factory toArray converts to array', function () {
    $data = $this->tokenInstance->toArray();

    expect($data)->toBeArray()->toHaveLength(4)
        ->and($data['access_token'])->toBeString()
        ->and($data['refresh_token'])->toBeString()
        ->and($data['access_token_expires_at'])->toBeString()
        ->and($data['refresh_token_expires_at'])->toBeString();
});
