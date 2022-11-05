<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Interfaces\{
    SqlInterface,
    ValueInterface,
};
use Stringable;

class StringValue implements SqlInterface, ValueInterface
{
    private $value;

    public function __construct(string|Stringable $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return "'{$this->value}'";
    }
}
