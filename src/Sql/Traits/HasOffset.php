<?php

declare(strict_types=1);

namespace QueryBuilder\Sql\Traits;

use QueryBuilder\Sql\Operators\Offset;

trait HasOffset
{
    private ?Offset $offset;

    public function offset(int $offset)
    {
        $this->offset = new Offset(null, $offset);
        return $this;
    }
}
