<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Commands\Dml;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\{
    SqlInterface,
    ValueInterface,
};
use QueryBuilder\Sql\Values\CollectionValue;

class Insert implements SqlInterface
{
    private string $tableName;
    private ValueInterface $columns;
    private array $values;
    private $isIgnoreStatement = false;

    public function __construct(string $tableName, array $data)
    {
        $data = $this->formatData($data);

        $this->tableName = $tableName;
        $this->values = $this->formatValuesFromData($data);
        $this->columns = $this->formatColumnsFromData($data);
    }

    private function formatValuesFromData(array $data): array
    {
        $values = [];

        foreach($data as $value) {
            $values[] = new CollectionValue($value);
        }

        return $values;
    }

    private function formatData(array $data): array
    {
        if($this->isNotDataAListOfArrays($data)) {
            return [$data];
        }

        return $data;
    }

    private function isNotDataAListOfArrays(array $data): bool
    {
        foreach($data as $fields) {
            if(!is_array($fields)) {
                return true;
            }
        }

        return false;
    }

    private function formatColumnsFromData(array $data): CollectionValue
    {
        $columns = [];

        foreach($data as $fields) {
            $fieldsColumns = $this->getFieldsColumns($fields);
            $columns = [...$columns, ...$fieldsColumns];
        }

        return $this->getFormattedColumns($columns);
    }

    private function getFieldsColumns(array $fields): array
    {
        return array_keys($fields);
    }

    private function getFormattedColumns(array $columns): ValueInterface
    {
        $columns = array_unique($columns);
        $columns = array_map($this->getFormattedColumn(...), $columns);

        return new CollectionValue($columns);
    }

    private function getFormattedColumn(string $column): ValueInterface
    {
        return ValueFactory::createRawValue("`${column}`");
    }

    public function __toString(): string
    {
        if($this->isIgnoreStatement) {
            return "INSERT IGNORE INTO `{$this->getTableName()}` {$this->getColumns()} VALUES {$this->getValuesToSql()}";
        }

        return "INSERT INTO `{$this->getTableName()}` {$this->getColumns()} VALUES {$this->getValuesToSql()}";
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getColumns(): ValueInterface
    {
        return $this->columns;
    }

    private function getValuesToSql(): string
    {
        return implode(', ', $this->getValues());
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function ignore(): self
    {
        $this->isIgnoreStatement = true;
        return $this;
    }
}
