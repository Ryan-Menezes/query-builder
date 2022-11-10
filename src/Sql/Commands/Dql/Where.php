<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Commands\Dql;

use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Sql\{
    Column,
    Field,
};

class Where implements SqlInterface
{
    private array $logicalInstructions = [];

    public function and(string|Column $column, string $operator, mixed $value): self
    {
        $field = new Field($column, $operator, $value);

        $this->addLogicalInstruction('AND', $field);

        return $this;
    }

    public function or(string|Column $column, string $operator, mixed $value): self
    {
        $field = new Field($column, $operator, $value);

        $this->addLogicalInstruction('OR', $field);

        return $this;
    }

    private function addLogicalInstruction(string $conditional, Field $field): void
    {
        if($this->isEmptyLogicalInstructionsArray()) {
            $this->logicalInstructions[] = $field;
            return;
        }

        $this->logicalInstructions[] = $conditional;
        $this->logicalInstructions[] = $field;
    }

    private function isEmptyLogicalInstructionsArray(): bool
    {
        return empty($this->getLogicalInstructions());
    }

    public function __toString(): string
    {
        $sqlFields = implode(' ', $this->getLogicalInstructions());
        return "WHERE {$sqlFields}";
    }

    public function getLogicalInstructions(): array
    {
        return $this->logicalInstructions;
    }
}
