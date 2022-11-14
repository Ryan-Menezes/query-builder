<?php

declare(strict_types=1);

namespace QueryBuilder\Interfaces;

interface FieldGeneratorInterface extends SqlInterface
{
    public function getField();
}
