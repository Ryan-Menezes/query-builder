<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Traits;

use QueryBuilder\Sql\Operators\Limit;

trait HasLimit
{
    private ?Limit $limit = null;

    public function limit(int $value): self
    {
        $this->limit = new Limit($value);
        return $this;
    }

    public function take(int $value): self
    {
        return $this->limit($value);
    }
}
