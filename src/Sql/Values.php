<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Interfaces\{
    SqlInterface,
    ValueInterface,
};
use QueryBuilder\Exceptions\InvalidArgumentValueException;
use QueryBuilder\Utils\SimpleIterator;
use QueryBuilder\Sql\Values\{
    StringValue,
    NumberValue,
    BooleanValue,
};

class Values extends SimpleIterator implements SqlInterface
{
    public function __construct(array $items = [])
    {
        parent::__construct([]);

        foreach($items as $item) {
            $this->add($item);
        }
    }

    public function __toString(): string
    {
        return implode(', ', $this->all());
    }

    public function all(): array
    {
        return $this->items;
    }

    public function add(mixed $item): self
    {
        $this->items[] = $this->parseItem($item);
        return $this;
    }

    private function parseItem(mixed $item): ValueInterface
    {
        if($item instanceof ValueInterface) {
            return $item;
        }

        if(is_string($item) && !empty($item)) {
            return new StringValue($item);
        }

        if(is_numeric($item)) {
            return new NumberValue($item);
        }

        if(is_bool($item)) {
            return new BooleanValue($item);
        }

        throw new InvalidArgumentValueException();
    }
}
