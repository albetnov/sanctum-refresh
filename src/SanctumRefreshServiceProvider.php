<?php

namespace Albet\SanctumRefresh;

use Albet\SanctumRefresh\Commands\PruneToken;
use Albet\SanctumRefresh\Models\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
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
            ->hasMigration('add_refresh_token_to_personal_access_token')
            ->hasCommand(PruneToken::class);
    }

    public function boot()
    {
        parent::boot();

        // use this package model instead of sanctum original model.
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
