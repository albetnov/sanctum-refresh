<?php

use Albet\SanctumRefresh\Facades\SanctumRefresh as SanctumRefreshFacade;
use Albet\SanctumRefresh\SanctumRefresh;

it('Ensure facade has a correct accessor', function () {
    SanctumRefreshFacade::shouldReceive('routes')
    ->once();

    SanctumRefresh::routes();
});
