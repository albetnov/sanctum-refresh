<?php

namespace Albet\SanctumRefresh\Helpers;

use Carbon\Carbon;
use DateTimeInterface;

class Calculate
{
    public static function estimateRefreshToken(DateTimeInterface|int|string $created_at): Carbon
    {
        return Carbon::parse($created_at)->addMinutes(config('sanctum-refresh.refresh_expiration'));
    }
}
