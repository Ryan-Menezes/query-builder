<?php

namespace Tests\Sql\Operators\Logical;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Factories\FieldFactory;
use QueryBuilder\Sql\Operators\Logical\Having;

/**
 * @requires PHP 8.1
 */
class HavingTest extends TestCase
{
    public function testShouldCreateHAVINGStatementsWithLogicalAndComparisonOperators()
    {
        $where = new Having();
        $where
            ->and(FieldFactory::createField('name', 'LIKE', '%any-name%'))
            ->or(FieldFactory::createField('salary', '>', 800));

        $this->assertEquals('HAVING name LIKE ? OR salary > ?', $where);
    }

    public function testShouldReturnAnEmptyStringIfThereIsNoLogicalComparison()
    {
        $where = new Having();

        $this->assertEquals('', $where);
    }
}
