<?php

use Albet\SanctumRefresh\Exceptions\SanctumRefreshException;
use Albet\SanctumRefresh\Traits\WithRefreshable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DummyModel extends Model
{
    use WithRefreshable;
}

class DummyNotModel
{
    use WithRefreshable;
}

it('inject accessToken relationship', function () {
    expect(method_exists(DummyModel::class, 'refreshToken'))->toBeTrue()
        ->and((new DummyModel())->refreshToken() instanceof HasOne)->toBeTrue();
});

it('throw model invalid', function () {
    expect(fn() => (new DummyNotModel())->refreshToken())
        ->toThrow(SanctumRefreshException::class, '[With Refreshable]: Model is not valid');
});
