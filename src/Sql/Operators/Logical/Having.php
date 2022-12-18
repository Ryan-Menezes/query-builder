<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Operators\Logical;

use QueryBuilder\Interfaces\SqlInterface;

class Having extends LogicalInstructions implements SqlInterface
{
    public function __toString(): string
    {
        if ($this->isEmptyLogicalInstructions()) {
            return '';
        }

        $sqlFields = parent::__toString();
        return "HAVING {$sqlFields}";
    }
}
