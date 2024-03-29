<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Sql\Sql;
use QueryBuilder\Interfaces\ValueInterface;
use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Exceptions\InvalidArgumentValueException;

class CollectionValue extends Sql implements ValueInterface
{
    private array $value;

    public function __construct(array $value)
    {
        $this->value = $this->formatValue($value);
    }

    private function formatValue(array $value): array
    {
        $newValue = [];

        foreach ($value as $v) {
            $newValue[] = $this->getFormattedValue($v);
        }

        return $newValue;
    }

    private function getFormattedValue(mixed $value): ValueInterface
    {
        if (is_array($value)) {
            throw new InvalidArgumentValueException(
                'Arrays are not accepted in the value collection.',
            );
        }

        return ValueFactory::createValue($value);
    }

    public function toSql(): string
    {
        $valueToSql = $this->formatValueToSql();
        return $valueToSql;
    }

    private function formatValueToSql(): string
    {
        $value = [];

        foreach ($this->value as $v) {
            $value[] = $this->getValueCorrespondingToItsType($v);
        }

        $valueToSql = implode(', ', $value);
        return "({$valueToSql})";
    }

    private function getValueCorrespondingToItsType(
        ValueInterface $value,
    ): string|ValueInterface {
        if ($this->isRawValue($value)) {
            return $value;
        }

        return '?';
    }

    private function isRawValue(ValueInterface $value): bool
    {
        return $value instanceof RawValue;
    }

    public function getValue(): array
    {
        return $this->value;
    }
}
