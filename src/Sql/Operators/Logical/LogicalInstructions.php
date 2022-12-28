<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Operators\Logical;

use QueryBuilder\Interfaces\{FieldInterface, LogicalInstructionsInterface};

abstract class LogicalInstructions implements LogicalInstructionsInterface
{
    private array $logicalInstructions = [];

    public function and(FieldInterface $field): self
    {
        $this->addLogicalInstruction('AND', $field);
        return $this;
    }

    public function or(FieldInterface $field): self
    {
        $this->addLogicalInstruction('OR', $field);
        return $this;
    }

    private function addLogicalInstruction(
        string $conditional,
        FieldInterface $field,
    ): void {
        if ($this->isEmptyLogicalInstructions()) {
            $this->logicalInstructions[] = $field;
            return;
        }

        $this->logicalInstructions[] = $conditional;
        $this->logicalInstructions[] = $field;
    }

    public function __toString(): string
    {
        if ($this->isEmptyLogicalInstructions()) {
            return '';
        }

        return implode(' ', $this->getLogicalInstructions());
    }

    protected function isEmptyLogicalInstructions(): bool
    {
        return empty($this->getLogicalInstructions());
    }

    public function getLogicalInstructions(): array
    {
        return $this->logicalInstructions;
    }
}
