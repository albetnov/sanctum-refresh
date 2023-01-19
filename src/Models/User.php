<?php

namespace Albet\SanctumRefresh\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string $email
 */
class User extends Model
{
    use HasApiTokens;
}
