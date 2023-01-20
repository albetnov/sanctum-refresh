<?php

namespace Albet\SanctumRefresh\Exceptions;

class MustExtendHasApiTokens extends \Exception
{
    public function __construct(string $model, int $code = 0, $previous = null)
    {
        parent::__construct("Your $model must extends Laravel\Sanctum\HasApiTokens", $code, $previous);
    }
}
