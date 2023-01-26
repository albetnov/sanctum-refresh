<?php

namespace Albet\SanctumRefresh\Commands;

use Albet\SanctumRefresh\Models\RefreshToken;
use Illuminate\Console\Command;

class PruneToken extends Command
{
    public $signature = 'prune:token';

    public $description = 'Prune expired token but instead from token expiration, use expiration from refresh.';

    public function handle(): int
    {
        // Find refresh token that associated with Access Token.
        // Figure out their expires_at and deletes them.
        $tokens = RefreshToken::with('accessToken')
            ->whereHas('accessToken', fn ($q) => $q->where('expires_at', '<', now()))
            ->where('expires_at', '<', now())
            ->get();

        // iterates through the token
        foreach ($tokens as $token) {
            // check if relationship match
            if ($token->accessToken !== null) {
                // delete both access token and refresh token
                $tokenId = $token->accessToken->id;
                $token->delete();
                \Laravel\Sanctum\PersonalAccessToken::find($tokenId)->delete();
            }
        }

        $this->info('Token cleared.');

        return self::SUCCESS;
    }
}
