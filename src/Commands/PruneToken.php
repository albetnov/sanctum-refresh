<?php

namespace Albet\SanctumRefresh\Commands;

use Albet\SanctumRefresh\Models\RefreshToken;
use Illuminate\Console\Command;

class PruneToken extends Command
{
    public $signature = 'prune:token';

    public $description = 'Prune expired token while considering the refresh token';

    public function handle(): int
    {
        // Find refresh token that associated with Access Token.
        // Figure out their expires_at and deletes them.
        $tokens = RefreshToken::with('accessToken')
            ->whereHas('accessToken', fn($q) => $q->where('expires_at', '<', now()))
            ->where('expires_at', '<', now())
            ->lazy();

        // iterates through the token
        foreach ($tokens as $token) {
            // check if relationship match
            if ($token->accessToken !== null) {
                $token->accessToken->delete();
                $token->delete();
            }
        }

        $this->info('Token cleared.');

        return self::SUCCESS;
    }
}
