<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Operators\Logical;

use QueryBuilder\Sql\Sql;
use QueryBuilder\Interfaces\{
    FieldInterface,
    LogicalInstructionsInterface,
    SqlInterface,
};

abstract class LogicalInstructions extends Sql implements
    LogicalInstructionsInterface
{
    private const SQL_AND_OPERATOR = 'AND';
    private const SQL_OR_OPERATOR = 'OR';

    protected SqlInterface $sql;
    private array $logicalInstructions;

    public function __construct(SqlInterface $sql)
    {
        $this->sql = $sql;
        $this->logicalInstructions = [];
    }

    public function and(FieldInterface $field): self
    {
        $this->addLogicalInstruction(self::SQL_AND_OPERATOR, $field);
        return $this;
    }

    public function or(FieldInterface $field): self
    {
        $this->addLogicalInstruction(self::SQL_OR_OPERATOR, $field);
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

    public function toSql(): string
    {
        if ($this->isEmptyLogicalInstructions()) {
            return '';
        }

        return implode(' ', $this->logicalInstructions);
    }

    protected function isEmptyLogicalInstructions(): bool
    {
        return empty($this->logicalInstructions);
    }
}
