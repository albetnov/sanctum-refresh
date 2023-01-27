<?php

namespace Albet\SanctumRefresh\Models;

use Albet\SanctumRefresh\SanctumRefresh;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * @property PersonalAccessToken $accessToken
 * @property DateTimeInterface $expires_at
 * @property string $token
 * @property int $token_id
 */
class RefreshToken extends Model
{
    protected $fillable = ['token', 'token_id', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected $hidden = ['token'];

    public function accessToken(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SanctumRefresh::$model, 'token_id', 'id');
    }
}
