<?php

namespace Albet\SanctumRefresh\Tests;

use Albet\SanctumRefresh\Facades\SanctumRefresh;
use Albet\SanctumRefresh\Models\PersonalAccessToken;
use Albet\SanctumRefresh\SanctumRefreshServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

use function Orchestra\Testbench\artisan;

class TestCase extends Orchestra
{
    protected $enablesPackageDiscoveries = true;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Albet\\SanctumRefresh\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        \Albet\SanctumRefresh\SanctumRefresh::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            SanctumRefreshServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'SanctumRefresh' => SanctumRefresh::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__.'/../vendor/laravel/sanctum/database/migrations');

        artisan($this, 'db:seed', ['--class' => 'Albet\SanctumRefresh\Tests\UserSeeder']);

        $migration = include __DIR__.'/../database/migrations/create_refresh_tokens_table.php.stub';
        $migration->up();
    }
}
