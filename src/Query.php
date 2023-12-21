<?php

declare(strict_types=1);

namespace QueryBuilder;

use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Sql\Sql;
use QueryBuilder\Sql\Traits\{
    HasJoin,
    HasSelect,
    HasWhere,
    HasLimit,
    HasOffset,
    HasOrderBy,
};

class Query extends Sql implements SqlInterface
{
    use HasSelect, HasJoin, HasWhere, HasOrderBy, HasLimit, HasOffset;

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
        $sqls = [
            $this->select->toSql(),
            $this->toSqlJoins(),
            $this->where->toSql(),
            $this->orderBy?->toSql() ?? '',
            $this->limit?->toSql() ?? '',
            $this->offset?->toSql() ?? '',
        ];

        $fullSql = (string) array_reduce(
            $sqls,
            function ($value, $sql) {
                $sql = trim($sql);

                if ($sql) {
                    $value .= " {$sql}";
                }

                return $value;
            },
            '',
        );

        return trim($fullSql);
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
