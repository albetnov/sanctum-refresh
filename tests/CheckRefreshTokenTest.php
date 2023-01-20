<?php

use Albet\SanctumRefresh\SanctumRefresh;
use Albet\SanctumRefresh\Tests\Tester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    SanctumRefresh::routes();
});

it('no refresh token provided', function () {
    $response = post('/refresh');

    $response->assertStatus(400);
});

it('invalid refresh token given', function () {
    Tester::generateFineFakeToken();

    $response = postJson('/refresh', [
        'refresh_token' => '1:invalid',
    ]);

    $response->assertStatus(400);
});
