<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Commands\Dml;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Sql\{
    Columns,
    TableName,
};
use QueryBuilder\Sql\Values\CollectionValue;

class Insert implements SqlInterface
{
    private TableName $tableName;
    private Columns $columns;
    private array $values;
    private $isIgnoreStatement = false;

    public function __construct(string $tableName, array $data)
    {
        $data = $this->formatData($data);

        $this->tableName = new TableName($tableName);
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

    private function formatColumnsFromData(array $data): Columns
    {
        $columns = [];

        foreach($data as $fields) {
            $fieldsColumns = $this->getFieldsColumns($fields);
            $columns = [...$columns, ...$fieldsColumns];
        }

        return $this->getUniqueColumns($columns);
    }

    private function getFieldsColumns(array $fields): array
    {
        return array_keys($fields);
    }

    private function getUniqueColumns(array $columns): Columns
    {
        $uniqueColumns = array_unique($columns);

        return new Columns($uniqueColumns);
    }

    public function __toString(): string
    {
        if($this->isIgnoreStatement) {
            return "INSERT IGNORE INTO `{$this->getTableName()}` ({$this->getColumns()}) VALUES {$this->getValuesToSql()}";
        }

        return "INSERT INTO `{$this->getTableName()}` ({$this->getColumns()}) VALUES {$this->getValuesToSql()}";
    }

    public function getTableName(): string
    {
        return $this->tableName->getTableName();
    }

    public function getColumns(): Columns
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
