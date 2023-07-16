<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Operators;

use QueryBuilder\Sql\SqlWithValues;
use QueryBuilder\Interfaces\SqlWithValuesInterface;

class Limit extends SqlWithValues implements SqlWithValuesInterface
{
    private ?SqlWithValuesInterface $sql;
    private int $value;

    public function __construct(?SqlWithValuesInterface $sql, int $value)
    {
        parent::__construct($sql?->getValues() ?? []);

        $this->sql = $sql;
        $this->value = $value;
    }

    public function toSql(): string
    {
        $sql = $this->sql?->toSql() ?? '';

        return trim("{$sql} LIMIT {$this->value}");
    }
}
