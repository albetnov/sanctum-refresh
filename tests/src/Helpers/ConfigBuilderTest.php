<?php

it("Generate the correct arrays", function () {
    $abilities = ['*'];
    $tokenExpiresAt = now()->addMinutes(20);
    $refreshTokenExpiresAt = now()->addHour();

    $result = config_builder(
        abilities: $abilities,
        tokenExpiresAt: $tokenExpiresAt,
        refreshTokenExpiresAt: $refreshTokenExpiresAt,
    );

    expect($result)->toBeArray()
        ->and($result['abilities'])->toEqual($abilities)
        ->and($result['token_expires_at'])->toEqual($tokenExpiresAt)
        ->and($result['refresh_expires_at'])->toEqual($refreshTokenExpiresAt);
});

it("has default value anyway", function() {
    $result = config_builder();

    expect($result)->toHaveKeys(['abilities', 'token_expires_at', 'refresh_expires_at']);
});
