<?php

declare(strict_types=1);

namespace QueryBuilder\Interfaces;

interface LogicalInstructionsInterface extends SqlInterface
{
    public function and(FieldInterface $field): self;
    public function or(FieldInterface $field): self;
}
