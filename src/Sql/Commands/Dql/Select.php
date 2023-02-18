<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Commands\Dql;

use QueryBuilder\Sql\SqlWithValues;
use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\SqlWithValuesInterface;
use QueryBuilder\Exceptions\{
    InvalidArgumentTableNameException,
    InvalidArgumentColumnNameException,
};

class Select extends SqlWithValues implements SqlWithValuesInterface
{
    private string $tableName;
    private array $columns;
    private bool $isDistinctStatement;

    public function __construct(
        string $tableName,
        array $columns = ['*'],
        array $values = [],
    ) {
        if (empty($tableName)) {
            throw new InvalidArgumentTableNameException(
                'The table name must be a string of length greater than zero.',
            );
        }

        parent::__construct($values);

        $this->tableName = $tableName;
        $this->columns = $this->formatAndValidateColumns($columns);
        $this->isDistinctStatement = false;
    }

    private function formatAndValidateColumns(array $columns): array
    {
        foreach ($columns as $k => $column) {
            if ($this->isInvalidColumnName($column)) {
                throw new InvalidArgumentColumnNameException(
                    'The column name must be a string of length greater than zero.',
                );
            }

            $columns[$k] = ValueFactory::createRawValue($column);
        }

        return $columns;
    }

    private function isInvalidColumnName(mixed $column): bool
    {
        return !is_string($column) || empty($column);
    }

    public function toSql(): string
    {
        $toString = 'SELECT';

        if ($this->isDistinctStatement) {
            $toString = "{$toString} DISTINCT";
        }

        $toString = "{$toString} {$this->getColumnsToString()} FROM `{$this->getTableName()}`";

        return $toString;
    }

    private function getColumnsToString(): string
    {
        $columns = $this->getColumns();
        return implode(', ', $columns);
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function distinct(): self
    {
        $this->isDistinctStatement = true;
        return $this;
    }
}
