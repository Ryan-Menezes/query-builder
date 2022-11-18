<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Where\Operators;

use InvalidArgumentException;
use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\FieldGeneratorInterface;
use QueryBuilder\Sql\Values\RawValue;
use QueryBuilder\Sql\{
    Column,
    Field,
};

class Between implements FieldGeneratorInterface
{
    private const SQL_BETWEEN_OPERATOR = 'BETWEEN';
    private const SQL_NOT_BETWEEN_OPERATOR = 'NOT BETWEEN';

    private string $column;
    private array $values;
    private bool $isNotOperator = false;

    public function __construct(string $column, array $values)
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

    public function getField(): Field
    {
        $sqlOperator = $this->getSqlOperator();
        $valuesToString = implode(' AND ', $this->values);

        $field = new Field($this->column, $sqlOperator, new RawValue($valuesToString));

        return $field;
    }

    private function getSqlOperator(): string
    {
        if($this->isNotOperator) {
            return self::SQL_NOT_BETWEEN_OPERATOR;
        }

        return self::SQL_BETWEEN_OPERATOR;
    }
}
