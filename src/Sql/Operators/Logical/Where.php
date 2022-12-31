<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Operators\Logical;

use QueryBuilder\Interfaces\{SqlInterface, LogicalInstructionsInterface};

class Where extends LogicalInstructions implements LogicalInstructionsInterface
{
    public function __construct(SqlInterface $sql)
    {
        parent::__construct($sql);
    }

    public function toSql(): string
    {
        if ($this->isEmptyLogicalInstructions()) {
            return '';
        }

        $sqlFields = parent::toSql();
        return "{$this->sql->toSql()} WHERE {$sqlFields}";
    }
}
