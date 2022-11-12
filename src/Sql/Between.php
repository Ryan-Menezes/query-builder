<?php

declare(strict_types=1);

namespace QueryBuilder\Sql;

use InvalidArgumentException;
use QueryBuilder\Sql\Values\RawValue;
use QueryBuilder\Factories\ValueFactory;

class Between
{
    private const SQL_BETWEEN_STATEMENT = 'BETWEEN';
    private const SQL_NOT_BETWEEN_STATEMENT = 'NOT BETWEEN';

    private Where $where;
    private string|Column $column;
    private array $values;

    public function __construct(Where $where, string|Column $column, array $values)
    {
        $this->where = $where;
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

    public function and(): void
    {
        $field = $this->generateField(self::SQL_BETWEEN_STATEMENT);
        $this->where->and($field);
    }

    public function andNot(): void
    {
        $field = $this->generateField(self::SQL_NOT_BETWEEN_STATEMENT);
        $this->where->or($field);
    }

    public function or(): void
    {
        $field = $this->generateField(self::SQL_BETWEEN_STATEMENT);
        $this->where->or($field);
    }

    public function orNot(): void
    {
        $field = $this->generateField(self::SQL_NOT_BETWEEN_STATEMENT);
        $this->where->or($field);
    }

    private function generateField(string $sqlStatement): Field
    {
        $valuesToString = implode(" AND ", $this->values);
        $field = new Field($this->column, $sqlStatement, new RawValue($valuesToString));

        return $field;
    }
}
