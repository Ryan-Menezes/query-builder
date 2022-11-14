<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Where;

use QueryBuilder\Interfaces\{
    FieldGeneratorInterface,
    SqlInterface,
};
use QueryBuilder\Sql\Field;

class Where implements SqlInterface
{
    private array $logicalInstructions = [];

    public function and(FieldGeneratorInterface|Field $field): self
    {
        $this->addLogicalInstruction('AND', $field);
        return $this;
    }

    public function or(FieldGeneratorInterface|Field $field): self
    {
        $this->addLogicalInstruction('OR', $field);
        return $this;
    }

    private function addLogicalInstruction(string $conditional, FieldGeneratorInterface|Field $field): void
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
