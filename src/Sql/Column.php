<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Exceptions\InvalidArgumentColumnException;
use QueryBuilder\Interfaces\SqlInterface;
use Stringable;

class Column implements SqlInterface
{
    private string $columnName;

    public function __construct(string|Stringable $columnName)
    {
        $this->columnName = trim($columnName, '`');

        if (empty($this->columnName)) {
            throw new InvalidArgumentColumnException('Past column name cannot be empty');
        }
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
