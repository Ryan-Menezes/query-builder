<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Traits;

use QueryBuilder\Sql\Commands\Dql\Select;

trait HasSelect
{
    private Select $select;

    private string $tableName;
    private array $columns = [];
    private array $values = [];

    private function startSelect(string $tableName): void
    {
        $this->tableName = $tableName;
        $this->select = new Select($tableName);
    }

    public function select(
        array|string $columns = ['*'],
        array $values = [],
    ): self {
        $this->columns = $this->parseColumns($columns);
        $this->values = $values;
        $this->select = new Select(
            $this->tableName,
            $this->columns,
            $this->values,
        );

        return $this;
    }

    public function distinct(): self
    {
        $this->select->distinct();

        return $this;
    }

    public function addSelect(array|string $columns, array $values = []): self
    {
        $columns = $this->parseColumns($columns);

        $this->columns = [...$this->columns, ...$columns];
        $this->values = $values;

        if ($this->select->isDistinct()) {
            $this->select = (new Select(
                $this->tableName,
                $this->columns,
                $this->values,
            ))->distinct();
        } else {
            $this->select = new Select(
                $this->tableName,
                $this->columns,
                $this->values,
            );
        }

        return $this;
    }

    private function parseColumns(array|string $columns): array
    {
        if (is_string($columns)) {
            return [$columns];
        }

        return $columns;
    }
}
