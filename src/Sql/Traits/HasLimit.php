<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Traits;

use QueryBuilder\Sql\Operators\Limit;

trait HasLimit
{
    private ?Limit $limit = null;

    public function limit(int $limit): self
    {
        $this->limit = new Limit($limit);
        return $this;
    }
}
