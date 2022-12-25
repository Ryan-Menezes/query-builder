<?php

declare(strict_types=1);

namespace QueryBuilder\Exceptions;

use InvalidArgumentException;

class InvalidArgumentValuesException extends InvalidArgumentException
{
    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
