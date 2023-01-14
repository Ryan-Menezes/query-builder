<?php

declare(strict_types=1);

namespace QueryBuilder\Interfaces;

interface SqlWithValuesInterface extends SqlInterface
{
    public function getValues(): array;
}
