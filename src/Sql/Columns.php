<?php

namespace QueryBuilder\Sql;

use Iterator;
use Countable;
use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Exceptions\InvalidArgumentColumnException;

class Columns implements SqlInterface, Iterator, Countable
{
    private $columns = [];

    public function __construct(array $columns = [])
    {
        $this->validateColumns($columns);
        $this->columns = array_unique($columns);
    }

    private function validateColumns(array $columns): void
    {
        foreach($columns as $key => $column) {
            if($this->isNotValidColumn($column)) {
                throw new InvalidArgumentColumnException($key);
            }
        }
    }

    private function isNotValidColumn($column): bool
    {
        return !is_string($column) || empty($column);
    }

    public function __toString(): string
    {
        return $this->parseColumnsToSTring();
    }

    private function parseColumnsToSTring() {
        $columns = $this->addBacktickAtTheBeginningAtTheEndOfEachColumn();
        return implode(', ', $columns);;
    }

    private function addBacktickAtTheBeginningAtTheEndOfEachColumn(): array
    {
        return array_map(function($column) {
            return "`${column}`";
        }, $this->all());
    }

    public function all(): array
    {
        return $this->columns;
    }

    public function add(string $column): self
    {
        if (!$this->has($column)) {
            $this->columns[] = $column;
        }

        return $this;
    }

    public function has(string $column): bool
    {
        return in_array($column, $this->all());
    }

    public function count(): int
    {
        return count($this->all());
    }

    public function current(): string
    {
        return current($this->columns);
    }

    public function key(): int
    {
        return key($this->columns);
    }

    public function next(): void
    {
        next($this->columns);
    }

    public function rewind(): void
    {
        reset($this->columns);
    }

    public function valid(): bool
    {
        return key($this->columns) !== null;
    }
}
