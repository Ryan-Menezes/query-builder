<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Interfaces\SqlInterface;
use Stringable;

class Column implements SqlInterface
{
    private string $columnName;

    public function __construct(string|Stringable $columnName)
    {
        $this->columnName = trim($columnName, '`');
    }

    public function __toString(): string
    {
        return "`{$this->getColumnName()}`";
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }
}
