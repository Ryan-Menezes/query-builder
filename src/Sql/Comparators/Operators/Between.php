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
use QueryBuilder\Sql\Values\{
    CollectionValue,
    RawValue,
};

class Between implements FieldInterface
{
    private const SQL_BETWEEN_OPERATOR = 'BETWEEN';
    private const SQL_NOT_BETWEEN_OPERATOR = 'NOT BETWEEN';

    private ValueInterface $column;
    private ValueInterface $values;
    private bool $isNotOperator = false;

    public function __construct(string $column, array $values)
    {
        $this->column = ValueFactory::createRawValue($column);
        $this->values = $this->formatValues($values);
    }

    private function formatValues(array $values): ValueInterface
    {
        if($this->isNotValidValues($values)) {
            throw new InvalidArgumentException('The array of values ​​must contain only two values');
        }

        foreach($values as $key => $value) {
            $values[$key] = ValueFactory::createValue($value);
        }

        return new CollectionValue($values);
    }

    private function isNotValidValues(array $values): bool
    {
        return count($values) !== 2;
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

    private function getField(): FieldInterface
    {
        $column = (string) $this->column;
        $operator = $this->getOperator();
        $valuesToString = $this->getValuesToString();

        $field = FieldFactory::createFieldWithRawValue($column, $operator, $valuesToString);

        return $field;
    }

    private function getValuesToString(): string
    {
        $values = [];

        foreach($this->values->getValue() as $v) {
            $values[] = $this->formatValueToString($v);
        }

        return implode(' AND ', $values);
    }

    private function formatValueToString(ValueInterface $value): string
    {
        if($this->isRawValue($value)) {
            return (string) $value;
        }

        return '?';
    }

    private function isRawValue(mixed $value): bool
    {
        return $value instanceof RawValue;
    }

    public function getOperator(): string
    {
        if($this->isNotOperator) {
            return self::SQL_NOT_BETWEEN_OPERATOR;
        }

        return self::SQL_BETWEEN_OPERATOR;
    }

    public function getColumn(): ValueInterface
    {
        $field = $this->getField();
        return $field->getColumn();
    }

    public function getValue(): ValueInterface
    {
        return $this->values;
    }
}
