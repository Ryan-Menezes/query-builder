<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Values;

use QueryBuilder\Interfaces\ValueInterface;
use QueryBuilder\Factories\ValueFactory;
use InvalidArgumentException;

class CollectionValue implements ValueInterface
{
    private array $value;

    public function __construct(array $value)
    {
        $this->value = $this->formatValue($value);
    }

    private function formatValue(array $value): array
    {
        $newValue = [];

        foreach($value as $v) {
            if(is_array($v)) {
                throw new InvalidArgumentException('Arrays are not accepted in the value collection');
            }

            $newValue[] = ValueFactory::createValue($v);
        }

        return $newValue;
    }

    public function __toString(): string
    {
        $valueToSql = $this->formatValueToSql();
        return $valueToSql;
    }

    private function formatValueToSql(): string
    {
        $value = [];

        foreach($this->value as $v) {
            $value[] = $this->getValueCorrespondingToItsType($v);
        }

        $valueToSql = implode(', ', $value);
        return "(${valueToSql})";
    }

    private function getValueCorrespondingToItsType(ValueInterface $value): string|ValueInterface
    {
        if($this->isRawValue($value)) {
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
