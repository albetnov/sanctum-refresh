<?php

use Albet\SanctumRefresh\Models\PersonalAccessToken;
use Albet\SanctumRefresh\Tests\Tester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class);

it('Pruned Old Token successfully', function () {
    Tester::generateFakeExprToken();

    Artisan::call('prune:token');

    $checkToken = PersonalAccessToken::first();

    assertNull($checkToken);
});

it('Not Prune Any Token', function () {
    Tester::generateFineFakeToken();

    Artisan::call('prune:token');

    $checkToken = PersonalAccessToken::first();

    assertTrue((bool) $checkToken);
});
