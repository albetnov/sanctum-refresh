<?php

use Albet\SanctumRefresh\Exceptions\SanctumRefreshException;
use Albet\SanctumRefresh\SanctumRefresh;
use Illuminate\Database\Eloquent\Model;

uses()->group('root');

it('can change personal access token model', function () {
    class PersonalAccessToken extends Model {}

    SanctumRefresh::usePersonalAccessTokenModel(PersonalAccessToken::class);

    expect(SanctumRefresh::$model)->toBe(PersonalAccessToken::class);
});

it('cannot change personal access token model (invalid class)', function () {
    class FakePersonalAccessToken {}

    SanctumRefresh::usePersonalAccessTokenModel(FakePersonalAccessToken::class);

    SanctumRefresh::usePersonalAccessTokenModel(FakePersonalAccessToken::class);
})->throws(SanctumRefreshException::class, '[Runtime Check] Invalid Model: FakePersonalAccessToken. Not a valid access token model');
