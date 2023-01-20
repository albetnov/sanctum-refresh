<?php

namespace Albet\SanctumRefresh\Controllers;

use Albet\SanctumRefresh\Requests\LoginRequest;
use Albet\SanctumRefresh\SanctumRefresh;
use Albet\SanctumRefresh\Services\Contracts\TokenIssuer;
use Albet\SanctumRefresh\Services\IssueToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function login(LoginRequest $request, IssueToken $issueToken): JsonResponse
    {
        $token = $issueToken->issue($request->auth());

        if ($token === TokenIssuer::AUTH_INVALID) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 403);
        }

        return response()->json(
            array_merge($token->getToken()->except('cookie')->toArray(), ['message' => SanctumRefresh::$authedMessage])
        )
            ->withCookie($token->getToken()->only('cookie')->toArray()['cookie']);
    }

    public function refresh(IssueToken $issueToken): JsonResponse
    {
        $newToken = $issueToken->refreshToken();

        return response()->json($newToken->getToken()->except('cookie')->toArray())
            ->withCookie($newToken->getToken()->only('cookie')->toArray()['cookie']);
    }
}
