<?php

namespace Albet\SanctumRefresh;

use Albet\SanctumRefresh\Commands\PruneToken;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SanctumRefreshServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('sanctum-refresh')
            ->hasConfigFile()
            ->hasMigration("add_refresh_token_to_personal_access_token")
            ->hasCommand(PruneToken::class);
    }
}
