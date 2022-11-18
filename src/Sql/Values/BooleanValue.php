<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Interfaces\ValueInterface;

class BooleanValue implements ValueInterface
{
    private bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->getValue() ? '1' : '0';
    }

    public function getValue(): bool
    {
        return $this->value;
    }
}
