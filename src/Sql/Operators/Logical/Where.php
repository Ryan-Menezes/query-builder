<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Operators\Logical;

use QueryBuilder\Interfaces\LogicalInstructionsInterface;

class Where extends LogicalInstructions implements LogicalInstructionsInterface
{
    public function toSql(): string
    {
        if ($this->isEmptyLogicalInstructions()) {
            return '';
        }

        $sqlFields = parent::toSql();
        return "WHERE {$sqlFields}";
    }
}
