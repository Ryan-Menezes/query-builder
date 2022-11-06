<?php

declare(strict_types=1);

namespace QueryBuilder\Interfaces;

interface ValueInterface extends SqlInterface
{
    public function getValue(): mixed;
}
