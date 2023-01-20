<?php

namespace Albet\SanctumRefresh\Tests;

use Albet\SanctumRefresh\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Tester
{
    public static function generateFakeExprToken(): void
    {
        DB::table('personal_access_tokens')->insert([
            'created_at' => Carbon::now()->subMinutes(40),
            'refresh_token' => Str::random(40),
            'token' => Str::random(40),
            'abilities' => '*',
            'name' => 'web',
            'tokenable_type' => 'Albet\SanctumRefresh\Models\User',
            'tokenable_id' => 1,
        ]);
    }

    public static function generateFineFakeToken(): void
    {
        $user = User::find(1);
        $user->tokens()->create([
            'refresh_token' => Str::random(40),
            'token' => Str::random(40),
            'abilities' => ['*'],
            'name' => 'web',
        ]);
    }
}
