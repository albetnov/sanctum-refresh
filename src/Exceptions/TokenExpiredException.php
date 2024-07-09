<?php

namespace Albet\SanctumRefresh\Exceptions;

class TokenExpiredException extends SanctumRefreshException
{
    public function __construct()
    {
        parent::__construct('Token already expired');
    }
}
