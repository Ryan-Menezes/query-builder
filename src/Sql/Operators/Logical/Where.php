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
        $parentToSql = $this->sql->toSql();
        $sqlFields = parent::toSql();

        if ($this->isEmptyLogicalInstructions()) {
            return $parentToSql;
        }

        return "${parentToSql} WHERE ${sqlFields}";
    }
}
