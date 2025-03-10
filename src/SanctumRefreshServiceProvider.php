<?php

namespace Albet\SanctumRefresh;

use Albet\SanctumRefresh\Commands\PruneToken;
use Albet\SanctumRefresh\Repositories\RefreshTokenRepository;
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
            ->hasMigration('create_refresh_tokens_table')
            ->hasCommand(PruneToken::class);
    }

    public function register()
    {
        parent::register();

        $this->app->singleton(
            RefreshTokenRepository::class,
            fn($app) => new RefreshTokenRepository()
        );
    }
}
