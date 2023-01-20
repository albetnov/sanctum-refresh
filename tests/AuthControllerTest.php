<?php

use Albet\SanctumRefresh\SanctumRefresh;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\call;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    SanctumRefresh::routes();
});

it('user can login', function () {
    $response = postJson('/login', [
        'email' => 'admin@mail.com',
        'password' => 'admin12345',
    ]);

    $response->assertOk()->assertJson(fn (AssertableJson $json) => $json
        ->hasAll(['token', 'expires_in', 'refresh_token', 'refresh_token_expires_in', 'message']))
    ->assertCookie('refresh_token');
});

it('error user wrong credentials', function () {
    $response = postJson('/login', [
        'email' => 'asal@mail.com',
        'password' => 'asal',
    ]);

    $response->assertForbidden()->assertJson(fn (AssertableJson $json) => $json->has('message')->etc());
});

it('use username instead of email', function () {
    $response = postJson('/login', [
        'username' => 'asal',
        'password' => 'asal',
    ]);

    $response->assertForbidden();
});

it('can refresh user token (from json)', function () {
    $response = postJson('/login', [
        'email' => 'admin@mail.com',
        'password' => 'admin12345',
    ]);

    $login = $response->assertOk()->json();

    $response = postJson('/refresh', [
        'refresh_token' => $login['refresh_token'],
    ]);

    $response->assertOk()->assertJson(fn (AssertableJson $json) => $json
        ->hasAll(['token', 'expires_in', 'refresh_token', 'refresh_token_expires_in']))
    ->assertCookie('refresh_token');
});

it('can refresh user token (from cookie)', function () {
    $response = postJson('/login', [
        'email' => 'admin@mail.com',
        'password' => 'admin12345',
    ]);

    $login = $response->assertOk()->assertCookie('refresh_token')->json();

    $response = call('POST', '/refresh', [], ['refresh_token' => $login['refresh_token']]);

    $response->assertOk()->assertJson(fn (AssertableJson $json) => $json
        ->hasAll(['token', 'expires_in', 'refresh_token', 'refresh_token_expires_in']))
    ->assertCookie('refresh_token');
});

it('failed refresh user token (invalid token given)', function () {
    $response = postJson('/refresh', [
        'refresh_token' => Str::random(40),
    ]);

    $response->assertStatus(400);
});
