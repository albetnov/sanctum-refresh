<?php

namespace Albet\SanctumRefresh\Models;

use Albet\SanctumRefresh\Traits\HasRefreshableToken;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string $email
 */
class User extends Model
{
    use HasApiTokens, HasRefreshableToken;
}
