<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Where;

use InvalidArgumentException;
use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\FieldGeneratorInterface;
use QueryBuilder\Sql\Values\RawValue;
use QueryBuilder\Sql\Field;

class In implements FieldGeneratorInterface
{
    private const SQL_IN_OPERATOR = 'IN';
    private const SQL_NOT_IN_OPERATOR = 'NOT IN';

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

    public function getField(): Field
    {
        $sqlOperator = $this->getSqlOperator();
        $valuesToString = implode(', ', $this->values);
        $valuesToString = "(${valuesToString})";

        $field = new Field($this->column, $sqlOperator, new RawValue($valuesToString));

        return $field;
    }

    private function getSqlOperator(): string
    {
        if($this->isNotOperator) {
            return self::SQL_NOT_IN_OPERATOR;
        }

        return self::SQL_IN_OPERATOR;
    }
}
