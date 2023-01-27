<?php

use Albet\SanctumRefresh\Models\PersonalAccessToken;
use Albet\SanctumRefresh\Models\RefreshToken;
use Albet\SanctumRefresh\SanctumRefresh;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('Can prune token successfully (both expired)', function () {
    DB::table('personal_access_tokens')->insert([
        'id' => 1,
        'name' => 'test',
        'token' => Str::random(40),
        'abilities' => '*',
        'expires_at' => now()->subMinutes(20),
        'tokenable_type' => 'Albet\SanctumRefresh\Models\User',
        'tokenable_id' => 1,
    ]);

    $token = PersonalAccessToken::first();

    RefreshToken::create([
        'token_id' => $token->id,
        'token' => Str::random(40),
        'expires_at' => now()->subMinutes(5),
    ]);

    Artisan::call('prune:token');

    expect(PersonalAccessToken::first())->toBeNull()
    ->and(RefreshToken::first())->toBeNull();
});

it("Didn't prune any token due to expiration (access not expired)", function () {
    DB::table('personal_access_tokens')->insert([
        'id' => 1,
        'name' => 'test',
        'token' => Str::random(40),
        'abilities' => '*',
        'expires_at' => now()->addMinutes(20),
        'tokenable_type' => 'Albet\SanctumRefresh\Models\User',
        'tokenable_id' => 1,
    ]);

    $token = PersonalAccessToken::first();

    RefreshToken::create([
        'token_id' => $token->id,
        'token' => Str::random(40),
        'expires_at' => now()->subMinutes(5),
    ]);

    expect(PersonalAccessToken::first())->not->toBeNull()
        ->and(RefreshToken::class)->not->toBeNull();
});

it("Didn't prune any token due to expiration (refresh not expired)", function () {
    DB::table('personal_access_tokens')->insert([
        'id' => 1,
        'name' => 'test',
        'token' => Str::random(40),
        'abilities' => '*',
        'expires_at' => now()->subMinutes(20),
        'tokenable_type' => 'Albet\SanctumRefresh\Models\User',
        'tokenable_id' => 1,
    ]);

    $token = PersonalAccessToken::first();

    RefreshToken::create([
        'token_id' => $token->id,
        'token' => Str::random(40),
        'expires_at' => now()->addMinutes(5),
    ]);

    expect(PersonalAccessToken::first())->not->toBeNull()
        ->and(RefreshToken::class)->not->toBeNull();
});
