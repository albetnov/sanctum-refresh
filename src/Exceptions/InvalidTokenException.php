<?php

namespace Albet\SanctumRefresh\Exceptions;

class InvalidTokenException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Your provided token is invalid.');
    }
}
