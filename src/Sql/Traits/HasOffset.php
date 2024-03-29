<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Traits;

use QueryBuilder\Sql\Operators\Offset;

trait HasOffset
{
    private ?Offset $offset = null;

    public function offset(int $value)
    {
        $this->offset = new Offset($value);
        return $this;
    }

    public function skip(int $value): self
    {
        return $this->offset($value);
    }
}
