<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Values\RawValue;
use QueryBuilder\Sql\{
    Where,
    Field,
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
            ->or(new Field('age', 'BETWEEN', new RawValue('20 AND 30')))
            ->or(new Field('created_at', '=', null));

        $this->assertEquals('WHERE `name` LIKE ? OR `salary` > ? AND `isTeacher` = ? OR `age` BETWEEN 20 AND 30 OR `created_at` = ?', $where);
        $this->assertEquals([
            new Field('name', 'LIKE', '%any-name%'),
            'OR',
            new Field('salary', '>', 800),
            'AND',
            new Field('isTeacher', '=', true),
            'OR',
            new Field('age', 'BETWEEN', new RawValue('20 AND 30')),
            'OR',
            new Field('created_at', '=', null),
        ], $where->getLogicalInstructions());
    }
}
