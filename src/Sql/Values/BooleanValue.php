<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Interfaces\{
    SqlInterface,
    ValueInterface,
};

class BooleanValue implements SqlInterface, ValueInterface
{
    private $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value ? '1' : '0';
    }
}
