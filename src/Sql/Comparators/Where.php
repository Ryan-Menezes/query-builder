<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Comparators;

use QueryBuilder\Interfaces\SqlInterface;

class Where extends LogicalInstructions implements SqlInterface
{
    public function __toString(): string
    {
        if ($this->isEmptyLogicalInstructions()) {
            return '';
        }

        $sqlFields = parent::__toString();
        return "WHERE {$sqlFields}";
    }
}
