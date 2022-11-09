<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Exceptions\InvalidArgumentColumnException;
use QueryBuilder\Utils\SimpleIterator;
use QueryBuilder\Sql\Values\RawValue;

class Columns extends SimpleIterator implements SqlInterface
{
    public function __construct(array $columns = [])
    {
        parent::__construct([]);

        $this->validateColumnsAndAdd($columns);
    }

    private function validateColumnsAndAdd(array $columns): void
    {
        foreach($columns as $key => $column) {
            if($this->isNotValidColumn($column)) {
                throw new InvalidArgumentColumnException("The column \"${key}\" of the array passed is not a valid column, a valid column must be of type Column or string");
            }

            $column = $this->formatColumnIfNecessary($column);

            $this->addColumnToItemsArray($column);
        }
    }

    private function isNotValidColumn(mixed $column): bool
    {
        return !($column instanceof Column) && !($column instanceof RawValue) && !is_string($column);
    }

    private function formatColumnIfNecessary(mixed $column): Column|RawValue
    {
        if(is_string($column)) {
            $column = new Column($column);
        }

        return $column;
    }

    private function addColumnToItemsArray(Column|RawValue $column): self
    {
        if ($this->hasNotColumn($column)) {
            $this->items[] = $column;
        }

        return $this;
    }

    private function hasNotColumn(Column|RawValue $column): bool
    {
        return !in_array($column, $this->all());
    }

    public function __toString(): string
    {
        return implode(', ', $this->all());
    }

    public function all(): array
    {
        return $this->items;
    }
}
