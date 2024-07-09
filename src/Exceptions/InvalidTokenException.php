<?php

namespace Albet\SanctumRefresh\Exceptions;

class InvalidTokenException extends SanctumRefreshException
{
    public function __construct()
    {
        parent::__construct('Token format is invalid');
    }
}
