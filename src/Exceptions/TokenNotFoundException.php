<?php

namespace Albet\SanctumRefresh\Exceptions;

class TokenNotFoundException extends SanctumRefreshException
{
    public function __construct()
    {
        return parent::__construct('Token not found');
    }
}
