<?php

use Albet\SanctumRefresh\Exceptions\InvalidModelException;
use Albet\SanctumRefresh\Traits\HasRefreshable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DummyModel extends Model {
    use HasRefreshable;
}

class DummyNotModel {
    use HasRefreshable;
}

it("inject accessToken relationship", function() {
   expect(method_exists(DummyModel::class, "refreshToken"))->toBeTrue()
   ->and((new DummyModel())->refreshToken() instanceof HasOne)->toBeTrue();
});

it("throw model invalid", function () {
    expect((new DummyNotModel())->refreshToken())->toThrow(InvalidModelException::class);
})->throws(InvalidModelException::class);
