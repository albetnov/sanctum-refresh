<?php

namespace Albet\SanctumRefresh\Controllers;

use Albet\SanctumRefresh\Exceptions\InvalidTokenException;
use Albet\SanctumRefresh\Exceptions\MustExtendHasApiTokens;
use Albet\SanctumRefresh\Requests\LoginRequest;
use Albet\SanctumRefresh\SanctumRefresh;
use Albet\SanctumRefresh\Services\Contracts\Token;
use Albet\SanctumRefresh\Services\TokenIssuer;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    /**
     * @throws MustExtendHasApiTokens
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $token = TokenIssuer::issue($request->auth());

        if ($token === Token::AUTH_INVALID) {
            return response()->json([
                'message' => 'Invalid Credentials!',
            ], 401);
        }

        return response()->json([
            'message' => SanctumRefresh::$authedMessage,
            'token' => $token->get('plain')['accessToken'],
            'token_expires_in' => $token->get('accessToken')->expires_at,
            'refresh_token' => $token->get('plain')['refreshToken'],
            'refresh_token_expires_in' => $token->get('refreshToken')->expires_at,
        ])
            ->withCookie(cookie('refresh_token', $token->get('plain')['refreshToken'], httpOnly: true));
    }

    public function refresh(): JsonResponse
    {
        $request = request();

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
            'token' => $newToken->get('plain')['accessToken'],
            'token_expires_in' => $newToken->get('accessToken')->expires_at,
            'refresh_token' => $newToken->get('plain')['refreshToken'],
            'refresh_token_expires_in' => $newToken->get('refreshToken')->expires_at,
        ])
            ->withCookie(cookie('refresh_token', $newToken->get('plain')['refreshToken'], httpOnly: true));
    }
}
