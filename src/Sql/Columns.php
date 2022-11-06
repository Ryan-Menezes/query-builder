<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Exceptions\InvalidArgumentColumnException;
use QueryBuilder\Utils\SimpleIterator;

class Columns extends SimpleIterator implements SqlInterface
{
    public function __construct(array $items = [])
    {
        parent::__construct([]);

        $this->validateColumnsAndAdd($items);
    }

    private function validateColumnsAndAdd(array $items): void
    {
        foreach($items as $key => $item) {
            if($this->isNotValidColumn($item)) {
                throw new InvalidArgumentColumnException("The column \"${key}\" of the array passed is not a valid column, a valid column must be of type Column or string");
            }

            if(is_string($item)) {
                $item = new Column($item);
            }

            $this->addColumnToItemsArray($item);
        }
    }

    private function isNotValidColumn(mixed $item): bool
    {
        return !($item instanceof Column) && !is_string($item);
    }

    private function addColumnToItemsArray(Column $item): self
    {
        if ($this->hasNotColumn($item)) {
            $this->items[] = $item;
        }

        return $this;
    }

    private function hasNotColumn(Column $item): bool
    {
        return !in_array($item, $this->all());
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
