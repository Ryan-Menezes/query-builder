<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Operators\Join;

use QueryBuilder\Sql\SqlWithValues;
use QueryBuilder\Interfaces\SqlWithValuesInterface;

class CrossJoin extends SqlWithValues implements SqlWithValuesInterface
{
    private ?SqlWithValuesInterface $sql;
    private string $tableName;

    public function __construct(
        string $tableName,
        ?SqlWithValuesInterface $sql = null,
    ) {
        parent::__construct($sql?->getValues() ?? []);

        $this->sql = $sql;
        $this->tableName = $tableName;
    }

    public function toSql(): string
    {
        $sql = $this->sql?->toSql() ?? '';

        return trim("{$sql} CROSS JOIN {$this->tableName}");
    }
}
