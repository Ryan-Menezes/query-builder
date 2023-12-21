<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Traits;

use QueryBuilder\Factories\FieldFactory;
use QueryBuilder\Sql\Operators\Join\CrossJoin;
use QueryBuilder\Sql\Operators\Join\FullJoin;
use QueryBuilder\Sql\Operators\Join\InnerJoin;
use QueryBuilder\Sql\Operators\Join\Join;
use QueryBuilder\Sql\Operators\Join\LeftJoin;
use QueryBuilder\Sql\Operators\Join\RightJoin;

trait HasJoin
{
    private array $joins = [];

    public function join(
        string $tableName,
        string|callable $column,
        string|null $operator = null,
        string|null $value = null,
    ): self {
        $join = new InnerJoin($tableName);

        return $this->executeJoin($join, $column, $operator, $value);
    }

    public function leftJoin(
        string $tableName,
        string|callable $column,
        string|null $operator = null,
        string|null $value = null,
    ): self {
        $join = new LeftJoin($tableName);

        return $this->executeJoin($join, $column, $operator, $value);
    }

    public function rightJoin(
        string $tableName,
        string|callable $column,
        string|null $operator = null,
        string|null $value = null,
    ): self {
        $join = new RightJoin($tableName);

        return $this->executeJoin($join, $column, $operator, $value);
    }

    public function fullJoin(
        string $tableName,
        string|callable $column,
        string|null $operator = null,
        string|null $value = null,
    ): self {
        $join = new FullJoin($tableName);

        return $this->executeJoin($join, $column, $operator, $value);
    }

    public function crossJoin(string $tableName): self
    {
        $join = new CrossJoin($tableName);
        $this->addJoin($join);

        return $this;
    }

    private function executeJoin(
        Join $join,
        string|callable $column,
        string|null $operator,
        string|null $value,
    ): self {
        if (is_callable($column)) {
            $column($join);
        } else {
            $field = FieldFactory::createFieldOnlyWithColumns(
                $column,
                $operator,
                $value,
            );
            $join->on($field);
        }

        $this->addJoin($join);

        return $this;
    }

    private function addJoin(Join $join): void
    {
        $this->joins[] = $join;
    }

    private function toSqlJoins(): string
    {
        return implode(' ', $this->joins);
    }
}
