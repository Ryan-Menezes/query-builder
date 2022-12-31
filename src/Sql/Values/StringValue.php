<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Sql\Sql;
use QueryBuilder\Interfaces\ValueInterface;
use Stringable;

class StringValue extends Sql implements ValueInterface
{
    private string $value;

    public function __construct(string|Stringable $value)
    {
        $this->value = (string) $value;
    }

    public function toSql(): string
    {
        return "'{$this->getValue()}'";
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
