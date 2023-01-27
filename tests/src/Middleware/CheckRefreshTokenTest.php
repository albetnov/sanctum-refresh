<?php

use Albet\SanctumRefresh\SanctumRefresh;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

beforeEach(function () {
    SanctumRefresh::routes();
});

it("no refresh token given", function () {
    $response = post("/refresh");

    $response->assertStatus(400)
        ->assertJson(fn(AssertableJson $json) => $json
            ->where('message', SanctumRefresh::$middlewareMsg));
});
