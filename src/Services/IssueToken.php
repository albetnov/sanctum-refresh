<?php

namespace Albet\SanctumRefresh\Services;

use Albet\SanctumRefresh\Models\PersonalAccessToken;
use Albet\SanctumRefresh\Requests\LoginRequest;
use Albet\SanctumRefresh\Services\Contracts\TokenIssuer;
use Illuminate\Http\Request;

class IssueToken
{
    public function issue(LoginRequest $request, string $tokenName = 'web', array $abilities = ['*']): TokenIssuer
    {
        $user = $request->auth();

        if(!$user) {
            return TokenIssuer::AUTH_INVALID;
        }

        $token = $user->createToken($tokenName, $abilities, now()->addMinutes(config('sanctum-refresh.expiration')));

        return new TokenIssuer($token);
    }

    public function refreshToken(Request $request): TokenIssuer
    {
        $refreshToken = $request->hasCookie('refresh_token') ?
            $request->cookie('refresh_token') :
            $request->get('refresh_token');

        // Parse the token id
        $tokenId = explode(':', $refreshToken)[0];

        // Find token from given id
        $token = PersonalAccessToken::find($tokenId);

        // Regenerate token.
        $newToken = $token->tokenable->createToken('web');

        // Delete current token (revoke refresh token)
        $token->delete();

        return new TokenIssuer($newToken);
    }
}
