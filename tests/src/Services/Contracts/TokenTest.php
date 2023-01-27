<?php

use Albet\SanctumRefresh\Models\RefreshToken;
use Albet\SanctumRefresh\Models\User;
use Albet\SanctumRefresh\Services\Contracts\Token;
use Illuminate\Support\Str;

it('Return AUTH_INVALID string', function () {
    $string = 'AUTH_INVALID';

    expect(Token::AUTH_INVALID)->toBe($string);
});

it('contain correct collection lists', function () {
    $token = User::find(1)->createToken('web',
        $config['abilities'] ?? ['*'],
        $config['token_expires_at'] ??
        now()->addMinutes(config('sanctum-refresh.expiration.access_token')));

    $plainRefreshToken = Str::random(40);

    $refreshToken = RefreshToken::create([
        'token' => hash('sha256', $plainRefreshToken),
        'expires_at' => $config['refresh_expires_at'] ??
            now()->addMinutes(config('sanctum-refresh.expiration.refresh_token')),
        'token_id' => $token->accessToken->id,
    ]);

    $tokenInstance = new Token($token, $plainRefreshToken, $refreshToken);

    expect($tokenInstance->getToken())->toHaveKeys(['refreshToken', 'accessToken', 'plain']);
});
