<?php

namespace Tests\Sql\Where;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Values\RawValue;
use QueryBuilder\Sql\{
    Field,
    Column,
};
use QueryBuilder\Sql\Where\{
    Where,
    Between,
};

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
            ->or(new Field('salary', '>', 800))
            ->and(new Field('isTeacher', '=', true))
            ->or(new Field('created_at', '=', null))
            ->or(new Field('updated_at', '<=', new RawValue('NOW()')));

        $this->assertEquals('WHERE `name` LIKE ? OR `salary` > ? AND `isTeacher` = ? OR `created_at` = ? OR `updated_at` <= NOW()', $where);
    }

    public function testShouldReturnAnEmptyStringIfThereIsNoLogicalComparison()
    {
        $where = new Where();

        $this->assertEquals('', $where);
    }

    public function testShouldSupportBETWEENStatements()
    {
        $where = new Where();
        $where
            ->and(new Between('age', [10, 20]))
            ->or(new Between('age', [10, new Column('any-column')]))
            ->and(
                (new Between('age', [10, 20]))->not()
            );

        $this->assertEquals('WHERE `age` BETWEEN 10 AND 20 OR `age` BETWEEN 10 AND `any-column` AND `age` NOT BETWEEN 10 AND 20', $where);
    }
}
