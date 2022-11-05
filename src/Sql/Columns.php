<?php

namespace QueryBuilder\Sql;

use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Exceptions\InvalidArgumentColumnException;
use QueryBuilder\Utils\SimpleIterator;
use Stringable;

class Columns extends SimpleIterator implements SqlInterface
{
    public function __construct(array $items = [])
    {
        $this->validateColumns($items);
        parent::__construct(array_unique($items));
    }

    private function validateColumns(array $items): void
    {
        foreach($items as $key => $item) {
            if($this->isNotValidColumn($item)) {
                throw new InvalidArgumentColumnException($key);
            }
        }
    }

    private function isNotValidColumn($item): bool
    {
        return !is_string($item) || empty($item);
    }

    public function __toString(): string
    {
        return $this->parseColumnsToSTring();
    }

    private function parseColumnsToSTring() {
        $items = $this->addBacktickAtTheBeginningAtTheEndOfEachColumn();
        return implode(', ', $items);;
    }

    private function addBacktickAtTheBeginningAtTheEndOfEachColumn(): array
    {
        return array_map(function($item) {
            return "`${item}`";
        }, $this->all());
    }

    public function all(): array
    {
        return $this->items;
    }

    public function add(string|Stringable $item): self
    {
        if ($this->hasNotColumn($item)) {
            $this->items[] = $item;
        }

        return $this;
    }

    private function hasNotColumn(string|Stringable $item): bool
    {
        return !in_array($item, $this->all());
    }
}
