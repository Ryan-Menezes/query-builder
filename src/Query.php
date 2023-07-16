<?php

declare(strict_types=1);

namespace QueryBuilder;

use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Sql\Operators\Logical\Where;
use QueryBuilder\Sql\Sql;
use QueryBuilder\Sql\Commands\Dql\Select;
use QueryBuilder\Sql\Traits\HasLimit;
use QueryBuilder\Sql\Traits\HasOffset;
use QueryBuilder\Sql\Traits\HasWhere;

class Query extends Sql implements SqlInterface
{
    use HasWhere, HasLimit, HasOffset;

    private string $tableName;
    private SqlInterface $sql;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
        $this->sql = new Select($tableName);
        $this->where = new Where();
        $this->offset = null;
    }

    public function toSql(): string
    {
        $sql = $this->sql->toSql();
        $where = $this->where->toSql();
        $limit = $this->limit?->toSql() ?? '';
        $offset = $this->offset?->toSql() ?? '';

        if ($where) {
            $sql .= " {$where}";
        }

        if ($limit) {
            $sql .= " {$limit}";
        }

        $sql = trim($sql);

        if ($offset) {
            $sql .= " {$offset}";
        }

        return trim($sql);
    }

    public function select(array $columns = ['*'], array $values = []): self
    {
        $this->sql = new Select($this->tableName, $columns, $values);
        return $this;
    }
}
