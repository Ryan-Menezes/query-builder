<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Sql\Sql;
use QueryBuilder\Interfaces\ValueInterface;

class NullValue extends Sql implements ValueInterface
{
    public function toSql(): string
    {
        return 'NULL';
    }

    public function getValue(): mixed
    {
        return null;
    }
}
