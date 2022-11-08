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
        return $this->getValueFormat($fieldsValue);
    }

    private function getValueFormat(array $value): array
    {
        $valueFormatted = [];

        foreach($value as $k => $v) {
            $valueFormatted[$k] = ValueFactory::createValue($v);
        }

        return $valueFormatted;
    }

    private function setColumns(array $columns): void
    {
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
            return "INSERT IGNORE INTO {$this->getTableName()} ({$this->getColumns()}) VALUES {$this->getValuesParseToSql()}";
        }

        return "INSERT INTO {$this->getTableName()} ({$this->getColumns()}) VALUES {$this->getValuesParseToSql()}";
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getColumns(): Columns
    {
        return $this->columns;
    }

    private function getValuesParseToSql(): string
    {
        $valueSql = $this->getValueFormatSql();

        $valuesParseToSql = array_map(fn () => $valueSql, $this->values);

        return implode(', ', $valuesParseToSql);
    }

    private function getValueFormatSql(): string
    {
        $interrogationValues = array_fill(0, $this->columns->count(), '?');
        $interrogationValues = implode(', ', $interrogationValues);

        return "(${interrogationValues})";
    }

    public function getValues(): array
    {
        if(count($this->values) === 1) {
            return $this->values[0];
        }

        return $this->values;
    }

    public function ignore(): self
    {
        $this->isIgnoreStatement = true;
        return $this;
    }
}
