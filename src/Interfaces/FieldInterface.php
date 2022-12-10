<?php

declare(strict_types=1);

namespace QueryBuilder\Interfaces;

interface FieldInterface extends SqlInterface
{
    public function getColumn(): ValueInterface;
    public function getOperator(): string;
    public function getValue(): ValueInterface;
}
