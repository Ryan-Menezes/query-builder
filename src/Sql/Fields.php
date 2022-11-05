<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Utils\SimpleIterator;

class Fields extends SimpleIterator implements SqlInterface
{
    private const INDEX_OPERATOR = 0;
    private const INDEX_VALUE = 1;
    private const VALUE_LENGTH_ARRAY_WITH_OPERATOR = 2;

    private Columns $columns;
    private Values $values;
    private array $operators = [];

    public function __construct(array $items = [])
    {
        parent::__construct([]);

        $columnsArray = $this->getColumnsOfArray($items);
        $valuesArray = $this->getValuesOfArray($items);

        $this->columns = new Columns($columnsArray);
        $this->values = new Values($valuesArray);
        $this->operators = $this->getOperatorsOfArray($this->columns->all(), $items);

        $this->combineColumnsWithValues();
    }

    private function getColumnsOfArray(array $items): array
    {
        return array_keys($items);
    }

    private function getValuesOfArray(array $items): array
    {
        $values = [];

        foreach($items as $column => $item) {
            $values[$column] = $this->getValueToItem($item);
        }

        return $values;
    }

    private function getValueToItem(mixed $item): mixed
    {
        $value = $item;

        if($this->isValidValueWithOperator($item)) {
            $value = $item[self::INDEX_VALUE];
        }

        return $value;
    }

    private function isValidValueWithOperator(mixed $item): bool
    {
        return  is_array($item) &&
                count($item) === self::VALUE_LENGTH_ARRAY_WITH_OPERATOR &&
                is_string($item[self::INDEX_OPERATOR]);
    }

    private function getOperatorsOfArray(array $columns, array $items): array
    {
        $items = array_combine($columns, array_values($items));
        $operators = [];

        foreach($columns as $column) {
            $item = $items[$column];
            $operators[$column] = $this->getOperatorToItem($item);
        }

        return $operators;
    }

    private function getOperatorToItem(mixed $item): string
    {
        $operator = '=';

        if($this->isValidValueWithOperator($item)) {
            $operator = $item[self::INDEX_OPERATOR];
        }

        return $operator;
    }

    private function combineColumnsWithValues()
    {
        $columns = $this->columns->all();
        $values = $this->values->all();
        $items = array_combine($columns, $values);

        foreach($items as $column => $value) {
            $operator = $this->operators[$column];
            $items[$column] = [$operator, $value];
        }

        $this->items = $items;
    }

    public function __toString(): string
    {
        $fieldValues = $this->convertsKeysAndValuesOfAnArrayToAnInlineValue();
        return implode(', ', $fieldValues);
    }

    private function convertsKeysAndValuesOfAnArrayToAnInlineValue(): array
    {
        $fieldValues = [];

        foreach($this->all() as $column => $item)
        {
            $operator = $this->getOperatorToItem($item);
            $value = $this->getValueToItem($item);
            $fieldValues[] = "${column} ${operator} ${value}";
        }

        return $fieldValues;
    }

    public function all(): array
    {
        return $this->items;
    }
}
