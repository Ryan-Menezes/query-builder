<?php

namespace QueryBuilder\Utils;

use Stringable;

class Str implements Stringable
{
    public string|Stringable $value;

    public function __construct(string|Stringable $value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string|Stringable
    {
        return $this->value;
    }

    public function after(string $search): string
    {
        $positionSearch = $this->indexOf($search);
        if($positionSearch === -1) {
            return '';
        }

        $positionSearch = $positionSearch + mb_strlen($search);
        return mb_substr($this->getValue(), $positionSearch);
    }

    public function before(string $search): string
    {
        $positionSearch = $this->indexOf($search);
        if($positionSearch === -1) {
            return '';
        }

        $positionSearch = $positionSearch;
        return mb_substr($this->getValue(), 0, $positionSearch);
    }

    public function indexOf(string $search): int
    {
        $index = mb_stripos($this->getValue(), $search);

        if($index === false) {
            return -1;
        }

        return $index;
    }

    public function length(): int
    {
        return mb_strlen($this->getValue());
    }
}
