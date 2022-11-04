<?php

namespace QueryBuilder\Exceptions;

use InvalidArgumentException;

class InvalidArgumentColumnException extends InvalidArgumentException
{
    public function __construct(int $key)
    {
        parent::__construct("The column \"${key}\" of the array passed is not a valid column, a valid column must be of type string");
    }
}
