<?php

declare(strict_types=1);

namespace QueryBuilder\Utils;

use Iterator;
use Countable;
use Serializable;

abstract class SimpleIterator implements Iterator, Countable, Serializable
{
    public function __construct(protected array $items)
    {
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function current(): mixed
    {
        return current($this->items);
    }

    public function key(): mixed
    {
        return key($this->items);
    }

    public function next(): void
    {
        next($this->items);
    }

    public function rewind(): void
    {
        reset($this->items);
    }

    public function valid(): bool
    {
        return key($this->items) !== null;
    }

    public function serialize(): string
    {
        return serialize($this->items);
    }

    public function unserialize(string $items): void
    {
        $this->items = unserialize($items);
    }
}
