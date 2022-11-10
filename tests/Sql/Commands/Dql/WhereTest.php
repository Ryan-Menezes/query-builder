<?php

namespace Tests\Sql\Commands\Dql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Commands\Dql\Where;
use QueryBuilder\Sql\Field;
use QueryBuilder\Sql\Values\RawValue;

/**
 * @requires PHP 8.1
 */
class WhereTest extends TestCase
{
    public function testShouldCreateWHEREStatementsWithLogicalAndComparisonOperators()
    {
        $where = new Where();
        $where
            ->and('name', 'LIKE', '%any-name%')
            ->or('salary', '>', 800)
            ->and('isTeacher', '=', true)
            ->or('age', 'BETWEEN', new RawValue('20 AND 30'))
            ->or('created_at', '=', null);

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
