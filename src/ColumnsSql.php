<?php

namespace QueryBuilder;

use Iterator;
use Countable;
use QueryBuilder\Interfaces\Sql;

class Columns implements Sql, Iterator, Countable
{
    private $columns = [];
    private $currentKey = 0;

    public function __toString()
    {
        return '';
    }

    public function add(string $column): void
    {
        $this->columns[] = $column;
    }

    public function count(): int
    {
        return count($this->columns);
    }

    public function current(): mixed
    {
        return $this->columns[$this->currentKey];
    }

    public function key(): mixed
    {
        return $this->$currentKey;
    }

    public function next(): void
    {
        $this->$currentKey++;
    }

    public function rewind(): void
    {
        $this->$currentKey = 0;
    }

    public function valid(): bool
    {
        return $this->count() > 0 && $this->currentKey < $this->count();
    }
}
