<?php

declare(strict_types=1);

namespace QueryBuilder;

use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Sql\Sql;
use QueryBuilder\Sql\Traits\{
    HasSelect,
    HasWhere,
    HasLimit,
    HasOffset,
    HasOrderBy,
};

class Query extends Sql implements SqlInterface
{
    use HasSelect, HasWhere, HasOrderBy, HasLimit, HasOffset;

    public function __construct(string $tableName)
    {
        $this->startSelect($tableName);
        $this->startWhere();
    }

    public static function table(string $tableName): self
    {
        return new self($tableName);
    }

    public function toSql(): string
    {
        $sql = $this->select->toSql();
        $where = $this->where->toSql();
        $orderBy = $this->orderBy?->toSql() ?? '';
        $limit = $this->limit?->toSql() ?? '';
        $offset = $this->offset?->toSql() ?? '';

        if ($where) {
            $sql .= " {$where}";
        }

        $sql = trim($sql);

        if ($orderBy) {
            $sql .= " {$orderBy}";
        }

        $sql = trim($sql);

        if ($limit) {
            $sql .= " {$limit}";
        }

        $sql = trim($sql);

        if ($offset) {
            $sql .= " {$offset}";
        }

        return trim($sql);
    }

    public function getValues(): array
    {
        return [
            ...$this->select->getValues(),
            ...$this->where->getValues(),
            ...$this->limit?->getValues() ?? [],
            ...$this->offset?->getValues() ?? [],
        ];
    }
}
