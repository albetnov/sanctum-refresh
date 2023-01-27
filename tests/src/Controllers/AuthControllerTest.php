<?php

use Albet\SanctumRefresh\Middleware\CheckRefreshToken;
use Albet\SanctumRefresh\Models\PersonalAccessToken;
use Albet\SanctumRefresh\SanctumRefresh;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\call;
use function Pest\Laravel\postJson;
use function Pest\Laravel\withoutMiddleware;

uses(RefreshDatabase::class);

beforeEach(function () {
    SanctumRefresh::usePersonalAccessTokenModel(PersonalAccessToken::class);
    SanctumRefresh::routes();
});

it('logged in successfully', function () {
    $response = postJson('/login', [
        'email' => 'admin@mail.com',
        'password' => 'admin12345',
    ]);

    $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json->hasAny([
            'message', 'token', 'token_expires_in', 'refresh_token', 'refresh_token_expires_in',
        ])->where('message', SanctumRefresh::$authedMessage)->etc())
        ->assertCookie('refresh_token', $response->json()['refresh_token'], false);
});

it('logged in failed wrong credentials', function () {
    $response = postJson('/login', [
        'email' => 'wrong@mail.com',
        'password' => 'wrong',
    ]);

    $response->assertUnauthorized()
        ->assertJson(fn (AssertableJson $json) => $json->where('message', 'Invalid Credentials!'));
});

it('refresh token success from post body', function () {
    $token = postJson('/login', [
        'email' => 'admin@mail.com',
        'password' => 'admin12345',
    ]);

    $response = postJson('/refresh', [
        'refresh_token' => $token->json()['refresh_token'],
    ]);

    $response->assertOk()->assertJson(fn (AssertableJson $json) => $json
        ->hasAny([
            'message', 'token', 'token_expires_in', 'refresh_token', 'refresh_token_expires_in',
        ]));
});

it('refresh token success from cookie', function () {
    $token = postJson('/login', [
        'email' => 'admin@mail.com',
        'password' => 'admin12345',
    ]);

    $response = call('POST', '/refresh', [], [
        'refresh_token' => $token->json()['refresh_token'],
    ]);

    $response->assertOk()->assertJson(fn (AssertableJson $json) => $json
        ->hasAny([
            'message', 'token', 'token_expires_in', 'refresh_token', 'refresh_token_expires_in',
        ]));
});

it('Failed refresh token due to invalidity (handled by middleware)', function () {
    $response = postJson('/refresh', [
        'refresh_token' => Str::random(40),
    ]);

    $response->assertStatus(400)
        ->assertJson(fn (AssertableJson $json) => $json->where('message', SanctumRefresh::$middlewareMsg)->etc());
});

it('Failed refresh token due to invalidity (handle by TokenIssuer)', function () {
    $response = withoutMiddleware(CheckRefreshToken::class)->postJson('/refresh', [
        'refresh_token' => Str::random(40),
    ]);

    $response->assertStatus(400)
        ->assertJson(fn (AssertableJson $json) => $json->where('message', SanctumRefresh::$middlewareMsg)->etc());
});
