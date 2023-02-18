<?php

declare(strict_types=1);

namespace QueryBuilder;

use QueryBuilder\Factories\FieldFactory;
use QueryBuilder\Interfaces\LogicalInstructionsInterface;
use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Sql\Operators\Logical\Where;
use QueryBuilder\Sql\Sql;

use QueryBuilder\Sql\Commands\Dql\Select;

class Query extends Sql implements SqlInterface
{
    private string $tableName;
    private SqlInterface $sql;
    private LogicalInstructionsInterface $where;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
        $this->sql = new Select($tableName);
    }

    public function toSql(): string
    {
        return $this->sql->toSql();
    }

    public function select(array $columns = ['*'], array $values = []): self
    {
        $this->sql = new Select($this->tableName, $columns, $values);
        return $this;
    }

    public function where(string $column, string $operator, mixed $value): self
    {
        $field = FieldFactory::createField($column, $operator, $value);
        $where = new Where($this->sql);
        $this->sql = $where->and($field);

        return $this;
    }

    public function orWhere(
        string $column,
        string $operator,
        mixed $value,
    ): self {
        $field = FieldFactory::createField($column, $operator, $value);
        $this->where = new Where($this->sql);
        $this->where->or($field);

        return $this;
    }
}
