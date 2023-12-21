<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Operators\Join;

use QueryBuilder\Interfaces\SqlWithValuesInterface;

class RightJoin extends Join
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
        $sqlOn = $this->on->toSql();

        return trim("{$sql} RIGHT JOIN {$this->tableName} {$sqlOn}");
    }
}
