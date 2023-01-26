<?php

namespace Albet\SanctumRefresh\Exceptions;

class InvalidModelException extends \Exception
{
    public function __construct($model)
    {
        parent::__construct("Your $model is invalid instance.");
    }
}
