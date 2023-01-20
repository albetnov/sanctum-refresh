<?php

use Albet\SanctumRefresh\SanctumRefresh;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route;
use function PHPUnit\Framework\assertEquals;

it('can custom authedMessage from given config', function () {
    $msg = 'Test';

    SanctumRefresh::routes([
        'authedMessage' => $msg,
    ]);

    assertEquals($msg, SanctumRefresh::$authedMessage);
});

it('can custom middlewareMessage', function () {
    $msg = 'Test';

    SanctumRefresh::routes([
        'middlewareMessage' => $msg,
    ]);

    assertEquals($msg, SanctumRefresh::$middlewareMsg);
});

it('registered all the routes successfully', function () {
    SanctumRefresh::routes();

    expect(isset(Route::getRoutes()->getRoutesByMethod(['POST'])['POST']['refresh']))->toBeTrue()
        ->and(isset(Route::getRoutes()->getRoutesByMethod(['POST'])['POST']['login']))->toBeTrue();
});

it('can custom loginUrl', function () {
    SanctumRefresh::routes([
        'loginUrl' => 'any',
    ]);

    expect(Route::getRoutes()->getRoutes()[0]->uri)->toEqual('any');
});

it('can custom refreshUrl', function () {
    SanctumRefresh::routes([
        'refreshUrl' => 'any',
    ]);

    expect(Route::getRoutes()->getRoutes()[1]->uri)->toEqual('any');
});

it('can add custom login middleware', function () {
    SanctumRefresh::routes([
        'loginMiddleware' => 'testfake',
    ]);

    expect(Route::getRoutes()->getRoutes()[0]->action['middleware'][0])->toEqual('testfake');

    Route::setRoutes(new RouteCollection()); // reset route

    SanctumRefresh::routes([
        'loginMiddleware' => ['oke', 'lah'],
    ]);

    expect(Route::getRoutes()->getRoutes()[0]->action['middleware'])->toHaveCount(2);
});

it('can add custom refresh middleware', function () {
    SanctumRefresh::routes([
        'refreshMiddleware' => 'test_single',
    ]);

    expect(Route::getRoutes()->getRoutes()[1]->action['middleware'])->toHaveCount(2)
        ->and(Route::getRoutes()->getRoutes()[1]->action['middleware'][0])->toEqual('checkRefreshToken');

    Route::setRoutes(new RouteCollection());
    SanctumRefresh::routes([
        'refreshMiddleware' => ['test_multi', 'middleware'],
    ]);

    expect(Route::getRoutes()->getRoutes()[1]->action['middleware'])->toHaveCount(3)
        ->and(Route::getRoutes()->getRoutes()[1]->action['middleware'][0])->toEqual('checkRefreshToken');
});

it('only show refresh route only', function () {
    SanctumRefresh::routes([
        'refreshOnly' => true,
    ]);

    expect(isset(Route::getRoutes()->getRoutesByMethod(['POST'])['POST']['refresh']))->toBeTrue()
        ->and(isset(Route::getRoutes()->getRoutesByMethod(['POST'])['POST']['login']))->toBeFalse();
});
