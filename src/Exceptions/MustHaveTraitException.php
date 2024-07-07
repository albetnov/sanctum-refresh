<?php

namespace Albet\SanctumRefresh\Exceptions;

class MustHaveTraitException extends \Exception
{
    public function __construct(string $model, string $trait, int $code = 0, $previous = null)
    {
        parent::__construct("Your $model must extends {$trait}", $code, $previous);
    }
}
