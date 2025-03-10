<?php

use Albet\SanctumRefresh\Tests\TestCase;

$groups = [
    'commands' => 'Commands',
    'factories' => 'Factories',
    'repositories' => 'Repositories',
    'services' => 'Services',
    'traits' => 'Traits',
];

foreach ($groups as $key => $value) {
    uses(TestCase::class)
        ->group($key)
        ->in(__DIR__ . "/src/$value");
}

uses(TestCase::class)->in(__DIR__ . '/src/HelpersTest.php', __DIR__ . '/src/SanctumRefreshTest.php');
