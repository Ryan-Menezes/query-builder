<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Where;

use InvalidArgumentException;
use QueryBuilder\Sql\Values\RawValue;
use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Interfaces\FieldGeneratorInterface;
use QueryBuilder\Sql\{
    Column,
    Field,
};

class Between implements FieldGeneratorInterface
{
    private const SQL_BETWEEN_STATEMENT = 'BETWEEN';
    private const SQL_NOT_BETWEEN_STATEMENT = 'NOT BETWEEN';

    private string|Column $column;
    private array $values;
    private bool $isNotStatement = false;

    public function __construct(string|Column $column, array $values)
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
        $this->isNotStatement = true;
        return $this;
    }

    public function __toString(): string
    {
        $field = $this->getField(self::SQL_BETWEEN_STATEMENT);

        return "${field}";
    }

    public function getField(): Field
    {
        $sqlStatement = $this->getSqlStatement();
        $valuesToString = implode(" AND ", $this->values);

        $field = new Field($this->column, $sqlStatement, new RawValue($valuesToString));

        return $field;
    }

    private function getSqlStatement(): string
    {
        if($this->isNotStatement) {
            return self::SQL_NOT_BETWEEN_STATEMENT;
        }

        return self::SQL_BETWEEN_STATEMENT;
    }
}
