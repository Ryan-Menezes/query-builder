<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Commands\Dml;

use QueryBuilder\Sql\Sql;
use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\{SqlWithValuesInterface, ValueInterface};
use QueryBuilder\Exceptions\{
    InvalidArgumentTableNameException,
    InvalidArgumentDataException,
};

class Insert extends Sql implements SqlWithValuesInterface
{
    private string $tableName;
    private ValueInterface $columns;
    private array $values;
    private bool $isIgnoreStatement = false;

    public function __construct(string $tableName, array $data)
    {
        if (empty($tableName)) {
            throw new InvalidArgumentTableNameException(
                'The table name must be a string of length greater than zero.',
            );
        }

        $data = $this->formatData($data);

        $this->tableName = $tableName;
        $this->values = $this->formatValuesFromData($data);
        $this->columns = $this->formatColumnsFromData($data);
    }

    private function formatValuesFromData(array $data): array
    {
        if (empty($data)) {
            throw new InvalidArgumentDataException(
                'The array of values ​​must contain at least one value to be inserted.',
            );
        }

        $values = [];

        foreach ($data as $value) {
            $values[] = ValueFactory::createCollectionValue($value);
        }

        return $values;
    }

    private function formatData(array $data): array
    {
        if ($this->isNotDataAListOfArrays($data)) {
            return [$data];
        }

        return $data;
    }

    private function isNotDataAListOfArrays(array $data): bool
    {
        foreach ($data as $fields) {
            if (!is_array($fields)) {
                return true;
            }
        }

        return false;
    }

    private function formatColumnsFromData(array $data): ValueInterface
    {
        $columns = [];

        foreach ($data as $fields) {
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

        return ValueFactory::createCollectionValue($columns);
    }

    private function getFormattedColumn(string $column): ValueInterface
    {
        return ValueFactory::createRawValue("`${column}`");
    }

    public function toSql(): string
    {
        if ($this->isIgnoreStatement) {
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
