<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Interfaces\ValueInterface;
use Stringable;

class StringValue implements ValueInterface
{
    private string $value;

    public function __construct(string|Stringable $value)
    {
        $this->value = (string) $value;
    }

    public function __toString(): string
    {
        return "'{$this->getValue()}'";
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
