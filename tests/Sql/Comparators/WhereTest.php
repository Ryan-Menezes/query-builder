<?php

namespace Tests\Sql\Comparators;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Values\RawValue;
use QueryBuilder\Sql\{
    Field,
    Column,
};
use QueryBuilder\Sql\Comparators\Where;
use QueryBuilder\Sql\Comparators\Operators\Between;

/**
 * @requires PHP 8.1
 */
class WhereTest extends TestCase
{
    public function testShouldCreateWHEREStatementsWithLogicalAndComparisonOperators()
    {
        $where = new Where();
        $where
            ->and(new Field('name', 'LIKE', '%any-name%'))
            ->or(new Field('salary', '>', 800));

        $this->assertEquals('WHERE `name` LIKE ? OR `salary` > ?', $where);
    }

    public function testShouldReturnAnEmptyStringIfThereIsNoLogicalComparison()
    {
        $where = new Where();

        $this->assertEquals('', $where);
    }
}
