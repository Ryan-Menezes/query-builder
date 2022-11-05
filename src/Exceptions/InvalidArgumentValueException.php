<?php

declare(strict_types=1);

namespace QueryBuilder\Exceptions;

use InvalidArgumentException;

class InvalidArgumentValueException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('There are invalid values ​​in the array, a valid value must be of type string, boolean, number or raw');
    }
}
