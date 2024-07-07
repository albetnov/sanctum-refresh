<?php

use Albet\SanctumRefresh\Exceptions\InvalidModelException;
use Albet\SanctumRefresh\SanctumRefresh;
use Illuminate\Database\Eloquent\Model;

it('can change personal access token model', function () {
    class PersonalAccessToken extends Model
    {
    }

    SanctumRefresh::usePersonalAccessTokenModel(PersonalAccessToken::class);

    expect(SanctumRefresh::$model)->toBe(PersonalAccessToken::class);
});

it('cannot change personal access token model (invalid class)', function () {
    class FakePersonalAccessToken
    {
    }

    SanctumRefresh::usePersonalAccessTokenModel(FakePersonalAccessToken::class);

    expect(SanctumRefresh::usePersonalAccessTokenModel(FakePersonalAccessToken::class))
        ->toThrow(InvalidModelException::class);
})->throws(InvalidModelException::class);
