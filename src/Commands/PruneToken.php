<?php

namespace Albet\SanctumRefresh\Commands;

use Albet\SanctumRefresh\Helpers\Calculate;
use Albet\SanctumRefresh\Models\PersonalAccessToken;
use Illuminate\Console\Command;

class PruneToken extends Command
{
    public $signature = 'prune:token';

    public $description = 'Prune expired token but instead from token expiration, use expiration from refresh.';

    public function handle(): int
    {
        $tokens = PersonalAccessToken::get();

        foreach ($tokens as $token) {
            $refreshExpr = Calculate::estimateRefreshToken($token->created_at);

            if ($refreshExpr->lte(now())) {
                $token->delete();
            }
        }

        $this->info('Token cleared successfully!');

        $this->info('All done');

        return self::SUCCESS;
    }
}
