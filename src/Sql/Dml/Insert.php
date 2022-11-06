<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Dml;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Sql\Columns;

class Insert implements SqlInterface
{
    private string $tableName;
    private Columns $columns;
    private array $values;

    public function __construct(string $tableName, array $items)
    {
        $arrayKeys = array_keys($items);
        $this->columns = new Columns($arrayKeys);

        $arrayValues = array_values($items);
        $this->values = $this->formatValues($arrayValues);

        $this->tableName = $this->formatTitle($tableName);
    }

    private function formatValues(array $values): array
    {
        foreach($values as $k => $v) {
            $values[$k] = ValueFactory::createValue($v);
        }

        return $values;
    }

    private function formatTitle(string $tableName): string
    {
        $tableName = trim($tableName, '`');
        return "`${tableName}`";
    }

    public function __toString(): string
    {
        $interrogationValues = array_fill(0, $this->columns->count(), '?');
        $interrogationValues = implode(', ', $interrogationValues);

        return "INSERT INTO {$this->getTableName()} ({$this->getColumns()}) VALUES ({$interrogationValues})";
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getColumns(): Columns
    {
        return $this->columns;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
