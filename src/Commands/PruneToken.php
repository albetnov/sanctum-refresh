<?php

namespace Albet\SanctumRefresh\Commands;

use Albet\SanctumRefresh\Models\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PruneToken extends Command
{
    public $signature = 'prune:token';

    public $description = 'Prune expired token but instead from token expiration, use expiration from refresh.';

    public function handle(): int
    {
        $tokens = PersonalAccessToken::get();

        foreach ($tokens as $token) {
            $refreshExpr = Carbon::parse($token->created_at)->addMinutes(config('sanctum-refresh.refresh_expiration'));

            if($refreshExpr->lte(now())) {
                $token->delete();
            }
        }

        $this->info("Token cleared successfully!");

        $this->info('All done');

        return self::SUCCESS;
    }
}
