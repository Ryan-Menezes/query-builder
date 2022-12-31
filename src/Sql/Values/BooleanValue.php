<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Sql\Sql;
use QueryBuilder\Interfaces\ValueInterface;

class BooleanValue extends Sql implements ValueInterface
{
    private bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function toSql(): string
    {
        return $this->getValue() ? '1' : '0';
    }

    public function getValue(): bool
    {
        return $this->value;
    }
}
