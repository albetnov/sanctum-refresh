<?php

namespace Albet\SanctumRefresh\Models;

use Albet\SanctumRefresh\SanctumRefresh;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property DateTimeInterface $expires_at
 * @property string $token
 * @property int $token_id
 *
 * @method static Builder check(string $token)
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

    public function scopeCheck(Builder $query, string $token)
    {
        return $query->where('expires_at', '>=', now())
            ->where('token', hash('sha256', $token));
    }
}
