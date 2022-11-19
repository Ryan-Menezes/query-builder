<?php

declare(strict_types=1);

namespace QueryBuilder\Interfaces;

use QueryBuilder\Sql\Column;

interface FieldInterface extends SqlInterface
{
    public function getColumn(): Column;
    public function getOperator(): string;
    public function getValue(): mixed;
}
