<?php

declare(strict_types=1);

namespace QueryBuilder\Interfaces;

use QueryBuilder\Sql\Values\CollectionValue;

interface SqlWithValuesInterface extends SqlInterface
{
    public function getValues(): array;
}
