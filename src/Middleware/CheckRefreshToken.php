<?php

namespace Albet\SanctumRefresh\Middleware;

use Albet\SanctumRefresh\Models\PersonalAccessToken;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckRefreshToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): RedirectResponse|Response|JsonResponse
    {
        // Check refresh token.
        $refreshToken = $request->hasCookie('refresh_token') ?
            $request->cookie('refresh_token') :
            $request->get('refresh_token');

        // Parse tokenId
        $tokenId = explode(':', $refreshToken)[0];

        // Check whenever the refresh token still valid or already expired
        $tokenModel = PersonalAccessToken::find($tokenId);
        $refreshExpr = Carbon::parse($tokenModel->created_at)->addMinutes(config('sanctum-refresh.refresh_expiration'));

        // If the token is still valid, check if it matches the database token.
        if ($refreshExpr->gt(now()) && $refreshToken === $tokenModel->plain_refresh_token) {
            return $next($request);
        }

        // return bad request if the refresh token is neither invalid or expired.
        return response()->json([
            'message' => 'Refresh token is expired or invalid.',
        ], 400);
    }
}
