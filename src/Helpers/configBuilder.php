<?php

if (! function_exists('config_builder')) {
    function config_builder(array $abilities = ['*'],
                            ?DateTimeInterface $tokenExpiresAt = null,
                            ?DateTimeInterface $refreshTokenExpiresAt = null)
    {
        return [
            'abilities' => $abilities,
            'token_expires_at' => $tokenExpiresAt,
            'refresh_expires_at' => $refreshTokenExpiresAt,
        ];
    }
}
