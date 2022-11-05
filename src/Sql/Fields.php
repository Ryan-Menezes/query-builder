<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Utils\SimpleIterator;

class Fields extends SimpleIterator implements SqlInterface
{
    private Columns $columns;
    private Values $values;

    public function __construct(array $items = [])
    {
        parent::__construct([]);

        $keysArray = array_keys($items);
        $valuesArray = array_values($items);

        $this->columns = new Columns($keysArray);
        $this->values = new Values($valuesArray);

        $this->combineColumnsWithValues();
    }

    private function combineColumnsWithValues()
    {
        $columns = $this->columns->all();
        $values = $this->values->all();

        $this->items = array_combine($columns, $values);
    }

    public function __toString(): string
    {
        $fieldValues = $this->convertsKeysAndValuesOfAnArrayToAnInlineValue();
        return implode(', ', $fieldValues);
    }

    private function convertsKeysAndValuesOfAnArrayToAnInlineValue(): array
    {
        $fieldValues = [];

        foreach($this->all() as $key => $value)
        {
            $fieldValues[] = "${key} = ${value}";
        }

        return $fieldValues;
    }

    public function all(): array
    {
        return $this->items;
    }
}
