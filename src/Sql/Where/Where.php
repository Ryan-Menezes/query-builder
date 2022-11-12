<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Where;

use QueryBuilder\Interfaces\SqlInterface;
use QueryBuilder\Sql\{
    Column,
    Field,
};

class Where implements SqlInterface
{
    private array $logicalInstructions = [];

    public function and(Field $field): self
    {
        $this->addLogicalInstruction('AND', $field);
        return $this;
    }

    public function or(Field $field): self
    {
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

    public function between(string|Column $column, array $values): self
    {
        $between = new Between($this, $column, $values);
        $between->and();

        return $this;
    }

    public function notBetween(string|Column $column, array $values): self
    {
        $between = new Between($this, $column, $values);
        $between->andNot();

        return $this;
    }

    public function orBetween(string|Column $column, array $values): self
    {
        $between = new Between($this, $column, $values);
        $between->or();

        return $this;
    }

    public function orNotBetween(string|Column $column, array $values): self
    {
        $between = new Between($this, $column, $values);
        $between->orNot();

        return $this;
    }
}
