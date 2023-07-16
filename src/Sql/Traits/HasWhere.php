<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Traits;

use QueryBuilder\Factories\FieldFactory;
use QueryBuilder\Sql\Operators\Logical\Where;

trait HasWhere
{
    private Where $where;

    public function where(string $column, string $operator, mixed $value): self
    {
        $field = FieldFactory::createField($column, $operator, $value);
        $this->where->and($field);

        return $this;
    }

    public function orWhere(
        string $column,
        string $operator,
        mixed $value,
    ): self {
        $field = FieldFactory::createField($column, $operator, $value);
        $this->where->or($field);

        return $this;
    }
}
