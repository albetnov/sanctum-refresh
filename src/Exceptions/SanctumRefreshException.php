<?php

namespace Albet\SanctumRefresh\Exceptions;

class SanctumRefreshException extends \Exception
{

    public function __construct(
        string $message,
        #[\SensitiveParameter] public readonly mixed $meta = null,
        public ?string $tag = null
    ) {
        parent::__construct($message);
    }
}
