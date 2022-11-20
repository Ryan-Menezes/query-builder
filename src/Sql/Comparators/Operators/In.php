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

class In implements FieldInterface
{
    private const SQL_IN_OPERATOR = 'IN';
    private const SQL_NOT_IN_OPERATOR = 'NOT IN';

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
            throw new InvalidArgumentException('The array of values ​​must not be empty');
        }

        foreach($values as $key => $value) {
            $values[$key]= $this->formatValue($value);
        }

        return $values;
    }

    private function isNotValidValues(array $values): bool
    {
        return empty($values);
    }

    private function formatValue(mixed $value): string
    {
        $value = ValueFactory::createValue($value);
        return (string) $value;
    }

    public function not(): self
    {
        $this->isNotOperator = true;
        return $this;
    }

    public function __toString(): string
    {
        $field = $this->getField(self::SQL_IN_OPERATOR);

        return "${field}";
    }

    private function getField(): Field
    {
        $column = (string) $this->column;
        $operator = $this->getOperator();
        $valuesToString = implode(', ', $this->values);
        $valuesToString = "(${valuesToString})";

        $field = FieldFactory::createFieldWithRawValue($column, $operator, $valuesToString);

        return $field;
    }

    public function getOperator(): string
    {
        if($this->isNotOperator) {
            return self::SQL_NOT_IN_OPERATOR;
        }

        return self::SQL_IN_OPERATOR;
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
