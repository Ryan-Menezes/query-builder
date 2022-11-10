<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Interfaces\ValueInterface;

class NullValue implements ValueInterface
{
    public function __toString(): string
    {
        return 'NULL';
    }

    public function getValue(): mixed
    {
        return null;
    }
}
