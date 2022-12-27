<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Commands\Dql;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\{SqlInterface};
use QueryBuilder\Exceptions\{
    InvalidArgumentTableNameException,
    InvalidArgumentColumnNameException,
};

class Select implements SqlInterface
{
    private string $tableName;
    private array $columns;
    private bool $isDistinctStatement = false;

    public function __construct(string $tableName, array $columns = ['*'])
    {
        if (empty($tableName)) {
            throw new InvalidArgumentTableNameException(
                'The table name must be a string of length greater than zero.',
            );
        }

        $this->tableName = $tableName;
        $this->columns = $this->formatAndValidateColumns($columns);
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

    public function __toString(): string
    {
        if ($this->isDistinctStatement) {
            return "SELECT DISTINCT {$this->getColumnsToString()} FROM `{$this->getTableName()}`";
        }

        return "SELECT {$this->getColumnsToString()} FROM `{$this->getTableName()}`";
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
