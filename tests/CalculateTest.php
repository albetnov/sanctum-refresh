<?php

use Albet\SanctumRefresh\Helpers\Calculate;
use Carbon\Carbon;
use function PHPUnit\Framework\assertEquals;

it('Gives correct calculation', function () {
    $now = Carbon::now();

    $refreshToken = Calculate::estimateRefreshToken($now->toDateTime());

    assertEquals(30, $refreshToken->diffInMinutes($now));
});
