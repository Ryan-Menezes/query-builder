<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Commands\Dml;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\{
    SqlInterface,
    ValueInterface,
};
use QueryBuilder\Sql\Columns;
use QueryBuilder\Sql\Values\RawValue;

class Insert implements SqlInterface
{
    private string $tableName;
    private Columns $columns;
    private array $values;
    private $isIgnoreStatement = false;

    public function __construct(string $tableName, array $data)
    {
        $data = $this->formatData($data);

        $this->setValuesAndColumns($data);

        $this->tableName = $this->formatTitle($tableName);
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

    private function setValuesAndColumns(array $data): void
    {
        $columns = [];

        foreach($data as $fields) {
            $fieldsColumns = $this->getFieldsColumns($fields);
            $columns = [...$columns, ...$fieldsColumns];

            $this->values[] = $this->getFieldsValue($fields);
        }

        $this->setColumns($columns);
    }

    private function getFieldsColumns(array $fields): array
    {
        return array_keys($fields);
    }

    private function getFieldsValue(array $fields): array
    {
        $fieldsValue = array_values($fields);
        return $this->getValueFormatted($fieldsValue);
    }

    private function getValueFormatted(array $value): array
    {
        $valueFormatted = [];

        foreach($value as $k => $v) {
            $valueFormatted[$k] = ValueFactory::createValue($v);
        }

        return $valueFormatted;
    }

    private function setColumns(array $columns): void
    {
        $columns = array_unique($columns);
        $this->columns = new Columns($columns);
    }

    private function formatTitle(string $tableName): string
    {
        $tableName = trim($tableName, '`');
        return "`${tableName}`";
    }

    public function __toString(): string
    {
        if($this->isIgnoreStatement) {
            return "INSERT IGNORE INTO {$this->getTableName()} ({$this->getColumns()}) VALUES {$this->getValuesToStringSql()}";
        }

        return "INSERT INTO {$this->getTableName()} ({$this->getColumns()}) VALUES {$this->getValuesToStringSql()}";
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getColumns(): Columns
    {
        return $this->columns;
    }

    private function getValuesToStringSql(): string
    {
        $valuesInline = $this->getValuesInline();

        return implode(', ', $valuesInline);
    }

    private function getValuesInline(): array
    {
        $values = $this->getValues();
        $valuesInline = [];

        foreach($values as $value) {
            $valuesInline[] = $this->getValueInline($value);
        }

        return $valuesInline;
    }

    private function getValueInline(array $value): string
    {
        $newValue = [];

        foreach($value as $k => $v) {
            $newValue[$k] = $this->getDefaultValue($v);
        }

        $valueToString = implode(', ', $newValue);

        return "(${valueToString})";
    }

    private function getDefaultValue(ValueInterface $value): string|ValueInterface
    {
        if($this->isRawValue($value)) {
            return $value;
        }

        return '?';
    }

    private function isRawValue(ValueInterface $value): bool
    {
        return $value instanceof RawValue;
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
