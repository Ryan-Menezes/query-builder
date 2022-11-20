<?php

namespace Tests\Sql\Comparators;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Factories\FieldFactory;
use QueryBuilder\Sql\Comparators\Where;

/**
 * @requires PHP 8.1
 */
class WhereTest extends TestCase
{
    public function testShouldCreateWHEREStatementsWithLogicalAndComparisonOperators()
    {
        $where = new Where();
        $where
            ->and(FieldFactory::createField('name', 'LIKE', '%any-name%'))
            ->or(FieldFactory::createField('salary', '>', 800));

        $this->assertEquals('WHERE `name` LIKE ? OR `salary` > ?', $where);
    }

    public function testShouldReturnAnEmptyStringIfThereIsNoLogicalComparison()
    {
        $where = new Where();

        $this->assertEquals('', $where);
    }
}
