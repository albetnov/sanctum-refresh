<?php

use Albet\SanctumRefresh\Exceptions\MustExtendHasApiTokens;
use Albet\SanctumRefresh\Services\IssueToken;
use Illuminate\Database\Eloquent\Model;

class FakeModel extends Model
{
}

it('Must throw exception when model with no HasApiTokens trait given', function () {
    $tokenizer = new IssueToken();

    expect($tokenizer->issue(new FakeModel()))
        ->toThrow(MustExtendHasApiTokens::class);
})->throws(MustExtendHasApiTokens::class);
