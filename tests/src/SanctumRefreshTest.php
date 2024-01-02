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

it('can custom authed message from config', function () {
    $msg = 'Test';
    config()->set('sanctum-refresh.message.authed', $msg);
    SanctumRefresh::boot(); // reboot.

    assertEquals($msg, SanctumRefresh::$authedMessage);
});

it('can custom invalid message from config', function () {
    $msg = 'Test';
    config()->set('sanctum-refresh.message.invalid', $msg);
    SanctumRefresh::boot(); // reboot.

    SanctumRefresh::routes();

    assertEquals($msg, SanctumRefresh::$middlewareMsg);
});

it('registered all the routes successfully', function () {
    SanctumRefresh::routes();

    expect(isset(Route::getRoutes()->getRoutesByMethod(['POST'])['POST']['refresh']))->toBeTrue()
        ->and(isset(Route::getRoutes()->getRoutesByMethod(['POST'])['POST']['login']))->toBeTrue();
});

it('can customize the login url', function () {
    SanctumRefresh::boot(); // reboot.

    SanctumRefresh::routes(loginUrl: 'any');

    expect(Route::getRoutes()->getRoutes()[0]->uri)->toEqual('any');
});

it('can customize the refresh url', function () {
    SanctumRefresh::boot(); // reboot.

    SanctumRefresh::routes(refreshUrl: 'any');

    expect(Route::getRoutes()->getRoutes()[1]->uri)->toEqual('any');
});

it('can add custom login middleware', function () {
    SanctumRefresh::boot(); // reboot.

    SanctumRefresh::routes(loginMiddlewares: 'testfake');

    expect(Route::getRoutes()->getRoutes()[0]->action['middleware'][0])->toEqual('testfake');

    Route::setRoutes(new RouteCollection()); // reset route
    SanctumRefresh::boot(); // reboot.

    SanctumRefresh::routes(loginMiddlewares: ['abc', 'def']);

    expect(Route::getRoutes()->getRoutes()[0]->action['middleware'])->toHaveCount(2);
});

it('can add custom refresh middleware', function () {
    SanctumRefresh::boot(); // reboot.
    SanctumRefresh::routes(refreshMiddlewares: 'testSingle');

    expect(Route::getRoutes()->getRoutes()[1]->action['middleware'][1])->toEqual('testSingle');

    Route::setRoutes(new RouteCollection());
    SanctumRefresh::boot(); // reboot.

    SanctumRefresh::routes(refreshMiddlewares: ['test_multi', 'middleware']);

    expect(Route::getRoutes()->getRoutes()[1]->action['middleware'])->toHaveCount(3)
        ->and(Route::getRoutes()->getRoutes()[1]->action['middleware'][1])->toEqual('test_multi')
        ->and(Route::getRoutes()->getRoutes()[1]->action['middleware'][2])->toEqual('middleware');
});

it('can show refresh route only', function () {
    SanctumRefresh::boot();

    SanctumRefresh::routes(refreshOnly: true);

    expect(isset(Route::getRoutes()->getRoutesByMethod(['POST'])['POST']['refresh']))->toBeTrue()
        ->and(isset(Route::getRoutes()->getRoutesByMethod(['POST'])['POST']['login']))->toBeFalse();
});

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
