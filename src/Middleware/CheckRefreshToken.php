<?php

namespace Albet\SanctumRefresh\Middleware;

use Albet\SanctumRefresh\Exceptions\InvalidTokenException;
use Albet\SanctumRefresh\Helpers\CheckForRefreshToken;
use Albet\SanctumRefresh\SanctumRefresh;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckRefreshToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): RedirectResponse|Response|JsonResponse
    {
        // Check refresh token.
        $refreshToken = $request->hasCookie('refresh_token') ?
            $request->cookie('refresh_token') :
            $request->get('refresh_token');

        if (! $refreshToken) {
            return response()->json([
                'message' => SanctumRefresh::$middlewareMsg,
            ], 400);
        }

        try {
            CheckForRefreshToken::check($refreshToken);

            return $next($request);
        } catch (InvalidTokenException $e) {
            return response()->json([
                'message' => SanctumRefresh::$middlewareMsg,
                'reason' => $e->getMessage(),
            ], 400);
        }
    }
}
