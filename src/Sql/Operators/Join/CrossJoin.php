<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Operators\Join;

use QueryBuilder\Interfaces\SqlWithValuesInterface;

class CrossJoin extends Join
{
    public function __construct(
        string $tableName,
        ?SqlWithValuesInterface $sql = null,
    ) {
        parent::__construct($tableName, $sql);
    }

    public function toSql(): string
    {
        $sql = $this->sql?->toSql() ?? '';

        return trim("{$sql} CROSS JOIN {$this->tableName}");
    }
}
