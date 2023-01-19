<?php

namespace Albet\SanctumRefresh\Controllers;


use Albet\SanctumRefresh\Requests\LoginRequest;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $request->auth();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 403);
        };

        $token = $user->createToken('web', ['*'], now()->addMinute(config('sanctum-refresh.expiration')));

        return response()->json([
            'message' => 'Authenticated successfully!',
            'token' => $token->plainTextToken,
            'expires_in' => $token->accessToken->expires_at,
            'refresh_token' => $token->accessToken->plain_refresh_token,
            'refresh_token_expires_in' => Carbon::parse($token->accessToken->created_at)
                ->addMinute(config('sanctum-refresh.refresh_expiration'))
        ])->withCookie(cookie('refresh_token', $token->accessToken->plain_refresh_token, 0, null, null, null, true));
    }

    public function refresh(Request $request): JsonResponse
    {
        // Get the refresh token from cookie
        $refreshToken = $request->hasCookie('refresh_token') ?
            $request->cookie('refresh_token') :
            $request->get('refresh_token');

        // Parse the token id
        $tokenId = explode(':', $refreshToken)[0];

        // Find token from given id
        $token = PersonalAccessToken::find($tokenId);

        // Regenerate token.
        $newToken = $token->tokenable->createToken("web");

        // Delete current token (revoke refresh token)
        $token->delete();

        // Return the token to api response.
        return response()->json([
            'token' => $newToken->plainTextToken,
            'expires_in' => $newToken->accessToken->expires_at,
            'refresh_token' => $newToken->accessToken->plain_refresh_token,
            'refresh_token_expires_in' => Carbon::parse($newToken->accessToken->created_at)
                ->addMinute(config('sanctum-refresh.refresh_expiration'))
        ]);
    }
}
