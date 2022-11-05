<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Exceptions\InvalidArgumentColumnException;
use QueryBuilder\Utils\SimpleIterator;
use Stringable;

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
                throw new InvalidArgumentColumnException($key);
            }

            $this->addColumnToItemsArrayAndParse($item);
        }
    }

    private function isNotValidColumn(mixed $item): bool
    {
        return !is_string($item) || empty($item);
    }

    private function addColumnToItemsArrayAndParse(string|Stringable $item): self
    {
        $itemWithBacktick = $this->addBacktickToItem($item);

        if ($this->hasNotColumn($itemWithBacktick)) {
            $this->items[] = $itemWithBacktick;
        }

        return $this;
    }

    private function addBacktickToItem(string|Stringable $item): string
    {
        if(!str_starts_with($item, '`')) {
            $item = "`${item}";
        }

        if(!str_ends_with($item, '`')) {
            $item = "${item}`";
        }

        return $item;
    }

    private function hasNotColumn(string|Stringable $item): bool
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
