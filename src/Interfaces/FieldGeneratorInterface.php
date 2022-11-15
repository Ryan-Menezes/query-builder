<?php

declare(strict_types=1);

namespace QueryBuilder\Interfaces;

use QueryBuilder\Sql\Field;

interface FieldGeneratorInterface extends SqlInterface
{
    public function getField(): Field;
}
