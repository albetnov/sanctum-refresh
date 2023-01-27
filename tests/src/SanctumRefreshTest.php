<?php

use Albet\SanctumRefresh\Exceptions\InvalidModelException;
use Albet\SanctumRefresh\SanctumRefresh;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route;
use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    Route::setRoutes(new RouteCollection()); // Reset route every test.
});

it('can custom authedMessage from given config', function () {
    $msg = 'Test';
    config()->set('sanctum-refresh.sanctum_refresh.message.authed', $msg);
    SanctumRefresh::boot(); // reboot.

    assertEquals($msg, SanctumRefresh::$authedMessage);
});

it('can custom invalidMsg', function () {
    $msg = 'Test';
    config()->set('sanctum-refresh.sanctum_refresh.message.invalidMsg', $msg);
    SanctumRefresh::boot(); // reboot.

    SanctumRefresh::routes();

    assertEquals($msg, SanctumRefresh::$middlewareMsg);
});

it('registered all the routes successfully', function () {
    SanctumRefresh::routes();

    expect(isset(Route::getRoutes()->getRoutesByMethod(['POST'])['POST']['refresh']))->toBeTrue()
        ->and(isset(Route::getRoutes()->getRoutesByMethod(['POST'])['POST']['login']))->toBeTrue();
});

it('can custom loginUrl', function () {
    config()->set('sanctum-refresh.sanctum_refresh.routes.urls.login', 'any');
    SanctumRefresh::boot(); // reboot.

    SanctumRefresh::routes();

    expect(Route::getRoutes()->getRoutes()[0]->uri)->toEqual('any');
});

it('can custom refreshUrl', function () {
    config()->set('sanctum-refresh.sanctum_refresh.routes.urls.refresh', 'any');
    SanctumRefresh::boot(); // reboot.

    SanctumRefresh::routes();

    expect(Route::getRoutes()->getRoutes()[1]->uri)->toEqual('any');
});

it('can add custom login middleware', function () {
    config()->set('sanctum-refresh.sanctum_refresh.routes.middlewares.login', 'testfake');
    SanctumRefresh::boot(); // reboot.

    SanctumRefresh::routes();

    expect(Route::getRoutes()->getRoutes()[0]->action['middleware'][0])->toEqual('testfake');

    Route::setRoutes(new RouteCollection()); // reset route
    config()->set('sanctum-refresh.sanctum_refresh.routes.middlewares.login', ['oke', 'lah']);
    SanctumRefresh::boot(); // reboot.

    SanctumRefresh::routes();

    expect(Route::getRoutes()->getRoutes()[0]->action['middleware'])->toHaveCount(2);
});

it('can add custom refresh middleware', function () {
    config()->set('sanctum-refresh.sanctum_refresh.routes.middlewares.refresh', 'testSingle');
    SanctumRefresh::boot(); // reboot.
    SanctumRefresh::routes();

    expect(Route::getRoutes()->getRoutes()[1]->action['middleware'][0])->toEqual('testSingle');

    Route::setRoutes(new RouteCollection());
    config()->set('sanctum-refresh.sanctum_refresh.routes.middlewares.refresh', ['test_multi', 'middleware']);
    SanctumRefresh::boot(); // reboot.

    SanctumRefresh::routes();

    expect(Route::getRoutes()->getRoutes()[1]->action['middleware'])->toHaveCount(2)
        ->and(Route::getRoutes()->getRoutes()[1]->action['middleware'][0])->toEqual('test_multi')
        ->and(Route::getRoutes()->getRoutes()[1]->action['middleware'][1])->toEqual('middleware');
});

it('only show refresh route only', function () {
    config()->set('sanctum-refresh.sanctum_refresh.routes.refreshOnly', true);
    SanctumRefresh::boot();

    SanctumRefresh::routes();

    expect(isset(Route::getRoutes()->getRoutesByMethod(['POST'])['POST']['refresh']))->toBeTrue()
        ->and(isset(Route::getRoutes()->getRoutesByMethod(['POST'])['POST']['login']))->toBeFalse();
});

it("can change personal access token model", function () {
    class PersonalAccessToken extends Model
    {
    }

    SanctumRefresh::usePersonalAccessTokenModel(PersonalAccessToken::class);

    expect(SanctumRefresh::$model)->toBe(PersonalAccessToken::class);
});

it("cannot change personal access token model (invalid class)", function () {
    class FakePersonalAccessToken
    {
    }

    SanctumRefresh::usePersonalAccessTokenModel(FakePersonalAccessToken::class);

    expect(SanctumRefresh::usePersonalAccessTokenModel(FakePersonalAccessToken::class))
        ->toThrow(InvalidModelException::class);
})->throws(InvalidModelException::class);
