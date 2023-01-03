<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Interfaces\SqlInterface;

abstract class Sql implements SqlInterface
{
    public function __toString(): string
    {
        return $this->toSql();
    }
}
