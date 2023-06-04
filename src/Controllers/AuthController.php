<?php

namespace Albet\SanctumRefresh\Controllers;

use Albet\SanctumRefresh\Exceptions\InvalidTokenException;
use Albet\SanctumRefresh\Exceptions\MustHaveTraitException;
use Albet\SanctumRefresh\Requests\LoginRequest;
use Albet\SanctumRefresh\SanctumRefresh;
use Albet\SanctumRefresh\Services\TokenIssuer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    /**
     * @throws MustHaveTraitException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $request->auth();

        if (! $user) {
            return response()->json([
                'message' => 'Invalid Credentials!',
            ], 401);
        }

        $token = TokenIssuer::issue($user);

        return response()->json([
            'message' => SanctumRefresh::$authedMessage,
            'token' => $token->token->plainTextToken,
            'token_expires_in' => $token->token->accessToken->expires_at, /* @phpstan-ignore-line */
            'refresh_token' => $token->plainRefreshToken,
            'refresh_token_expires_in' => $token->refreshToken->expires_at,
        ])->withCookie(cookie('refresh_token', $token->plainRefreshToken, httpOnly: true));
    }

    public function refresh(Request $request): JsonResponse
    {
        $refreshToken = $request->hasCookie('refresh_token') ?
            $request->cookie('refresh_token') :
            $request->get('refresh_token');

        try {
            $newToken = TokenIssuer::refreshToken($refreshToken);
        } catch (InvalidTokenException $e) {
            return response()->json([
                'message' => SanctumRefresh::$middlewareMsg,
            ], 400);
        }

        return response()->json([
            'token' => $newToken->token->plainTextToken,
            'token_expires_in' => $newToken->token->accessToken->expires_at, /* @phpstan-ignore-line */
            'refresh_token' => $newToken->plainRefreshToken,
            'refresh_token_expires_in' => $newToken->refreshToken->expires_at,
        ])->withCookie(cookie('refresh_token', $newToken->plainRefreshToken, httpOnly: true));
    }
}
