<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Commands\Dql;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\{LogicalInstructionsInterface, SqlInterface};
use QueryBuilder\Exceptions\{
    InvalidArgumentTableNameException,
    InvalidArgumentColumnNameException,
};

class Select implements SqlInterface
{
    private string $tableName;
    private array $columns;
    private bool $isDistinctStatement = false;
    private ?LogicalInstructionsInterface $logicalInstructions = null;

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
        $toString = 'SELECT';

        if ($this->isDistinctStatement) {
            $toString = "${toString} DISTINCT";
        }

        $toString = "${toString} {$this->getColumnsToString()} FROM `{$this->getTableName()}`";

        if ($this->hasLogicalInstructions()) {
            $toString = "${toString} {$this->getLogicalInstructions()}";
        }

        return $toString;
    }

    private function getColumnsToString(): string
    {
        $columns = $this->getColumns();
        return implode(', ', $columns);
    }

    private function hasLogicalInstructions(): bool
    {
        return (bool) $this->getLogicalInstructions();
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

    public function getLogicalInstructions(): ?LogicalInstructionsInterface
    {
        return $this->logicalInstructions;
    }

    public function setLogicalInstructions(
        LogicalInstructionsInterface $logicalInstructions,
    ): self {
        $this->logicalInstructions = $logicalInstructions;
        return $this;
    }
}
