<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Comparators\Operators;

use InvalidArgumentException;
use QueryBuilder\Factories\{
    FieldFactory,
    ValueFactory,
};
use QueryBuilder\Interfaces\{
    FieldInterface,
    ValueInterface,
};
use QueryBuilder\Sql\{
    Column,
    Field,
};

class Between implements FieldInterface
{
    private const SQL_BETWEEN_OPERATOR = 'BETWEEN';
    private const SQL_NOT_BETWEEN_OPERATOR = 'NOT BETWEEN';

    private Column $column;
    private array $values;
    private bool $isNotOperator = false;

    public function __construct(Column $column, array $values)
    {
        $this->column = $column;
        $this->values = $this->formatValues($values);
    }

    private function formatValues(array $values): array
    {
        if($this->isNotValidValues($values)) {
            throw new InvalidArgumentException('The array of values ​​must contain only two values');
        }

        foreach($values as $key => $value) {
            $values[$key]= $this->formatValue($value);
        }

        return $values;
    }

    private function isNotValidValues(array $values): bool
    {
        return count($values) !== 2;
    }

    private function formatValue(mixed $value): string
    {
        if($this->isNotColumnValue($value)) {
            $value = ValueFactory::createValue($value);
        }

        return (string) $value;
    }

    private function isNotColumnValue(mixed $value): bool
    {
        return !($value instanceof Column);
    }

    public function not(): self
    {
        $this->isNotOperator = true;
        return $this;
    }

    public function __toString(): string
    {
        $field = $this->getField(self::SQL_BETWEEN_OPERATOR);

        return "${field}";
    }

    private function getField(): Field
    {
        $column = (string) $this->column;
        $operator = $this->getOperator();
        $valuesToString = implode(' AND ', $this->values);

        $field = FieldFactory::createFieldWithRawValue($column, $operator, $valuesToString);

        return $field;
    }

    public function getOperator(): string
    {
        if($this->isNotOperator) {
            return self::SQL_NOT_BETWEEN_OPERATOR;
        }

        return self::SQL_BETWEEN_OPERATOR;
    }

    public function getColumn(): Column
    {
        $field = $this->getField();
        return $field->getColumn();
    }

    public function getValue(): ValueInterface|Column
    {
        $field = $this->getField();
        return $field->getValue();
    }
}
