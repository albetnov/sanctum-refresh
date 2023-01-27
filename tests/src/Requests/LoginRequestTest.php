<?php

use Albet\SanctumRefresh\Models\User;
use Albet\SanctumRefresh\Requests\LoginRequest;

it('authorize return true', function () {
    $request = new LoginRequest();

    expect($request->authorize())->toBeTrue();
});

it('rules return valid array', function () {
    $req = new LoginRequest();

    expect($req->rules())->toHaveKeys(['username', 'email', 'password']);
});

it('auth using email success', function () {
    $req = new LoginRequest();

    $req->merge([
        'email' => 'admin@mail.com',
        'password' => 'admin12345',
    ]);

    expect($req->auth())->toBeInstanceOf(User::class);
});

it('auth failed', function () {
    $req = new LoginRequest();

    $req->merge([
        'username' => 'test',
        'password' => 'test12345',
    ]);

    expect($req->auth())->toBeFalse();
});
