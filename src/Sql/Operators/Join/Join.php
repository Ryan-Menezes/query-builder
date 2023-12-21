<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Operators\Join;

use QueryBuilder\Interfaces\FieldInterface;
use QueryBuilder\Sql\SqlWithValues;
use QueryBuilder\Interfaces\SqlWithValuesInterface;
use QueryBuilder\Sql\Operators\Logical\On;

abstract class Join extends SqlWithValues implements SqlWithValuesInterface
{
    protected ?SqlWithValuesInterface $sql;
    protected string $tableName;
    protected On $on;

    public function __construct(
        string $tableName,
        ?SqlWithValuesInterface $sql = null,
    ) {
        parent::__construct($sql?->getValues() ?? []);

        $this->sql = $sql;
        $this->tableName = $tableName;
        $this->on = new On();
    }

    public function on(FieldInterface $field): self
    {
        $this->on->and($field);
        return $this;
    }

    public function orOn(FieldInterface $field): self
    {
        $this->on->or($field);
        return $this;
    }

    public function getValues(): array
    {
        return [...parent::getValues(), ...$this->on->getValues()];
    }
}
