<?php

namespace Albet\SanctumRefresh\Models;

use Albet\SanctumRefresh\Traits\HasRefreshable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonalAccessToken extends \Laravel\Sanctum\PersonalAccessToken
{
    use HasFactory, HasRefreshable;
}
