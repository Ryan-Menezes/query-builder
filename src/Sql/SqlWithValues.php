<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\{SqlInterface, SqlWithValuesInterface};

abstract class SqlWithValues extends Sql implements
    SqlInterface,
    SqlWithValuesInterface
{
    private array $values;

    public function __construct(array $values = [])
    {
        foreach ($values as $key => $value) {
            $values[$key] = ValueFactory::createValue($value);
        }

        $this->values = $values;
    }

    public function addValue(mixed $value): void
    {
        $this->values[] = ValueFactory::createValue($value);
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
