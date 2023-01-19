<?php

namespace Albet\SanctumRefresh\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * @property DateTimeInterface|string $created_at
 * @property string $plain_refresh_token
 * @property DateTimeInterface|string $expires_at
 * @property string $refresh_token
 * @property int $id
 */
class PersonalAccessToken extends \Laravel\Sanctum\PersonalAccessToken
{
    use HasFactory;

    // append plain refresh token
    protected $appends = ['plain_refresh_token'];

    protected static function boot()
    {
        parent::boot();

        // add refresh token
        static::creating(function ($item) {
            $item->refresh_token = Crypt::encryptString(Str::random(40));
        });
    }

    protected function plainRefreshToken(): Attribute
    {
        // Decrypt refresh token.
        return Attribute::make(
            get: fn () => $this->id.':'.Crypt::decryptString($this->refresh_token)
        );
    }
}
