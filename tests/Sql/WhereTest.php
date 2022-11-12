<?php

namespace Tests\Sql\Where;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Column;
use QueryBuilder\Sql\Values\RawValue;
use QueryBuilder\Sql\Field;
use QueryBuilder\Sql\Where\Where;

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
        $this->assertEquals([
            new Field('name', 'LIKE', '%any-name%'),
            'OR',
            new Field('salary', '>', 800),
            'AND',
            new Field('isTeacher', '=', true),
            'OR',
            new Field('created_at', '=', null),
            'OR',
            new Field('updated_at', '<=', new RawValue('NOW()')),
        ], $where->getLogicalInstructions());
    }

    /**
     * @dataProvider shouldCreateABetweenStatementCorrectlyProvider
     */
    public function testShouldCreateABetweenStatementCorrectly(array $values, string $expected)
    {
        $where = new Where();
        $where->between('any-column', $values);

        $this->assertEquals($expected, $where);
    }

    public function shouldCreateABetweenStatementCorrectlyProvider()
    {
        return [
            [[5, 10], 'WHERE `any-column` BETWEEN 5 AND 10'],
            [['2000-01-01', '2001-01-01'], 'WHERE `any-column` BETWEEN \'2000-01-01\' AND \'2001-01-01\''],
            [[new Column('a'), new Column('b')], 'WHERE `any-column` BETWEEN `a` AND `b`'],
            [[5, new Column('b')], 'WHERE `any-column` BETWEEN 5 AND `b`'],
            [[new Column('a'), 5], 'WHERE `any-column` BETWEEN `a` AND 5'],
        ];
    }

    /**
     * @dataProvider shouldCreateANotBetweenStatementCorrectlyProvider
     */
    public function testShouldCreateANotBetweenStatementCorrectly(array $values, string $expected)
    {
        $where = new Where();
        $where->notBetween('any-column', $values);

        $this->assertEquals($expected, $where);
    }

    public function shouldCreateANotBetweenStatementCorrectlyProvider()
    {
        return [
            [[5, 10], 'WHERE `any-column` NOT BETWEEN 5 AND 10'],
            [['2000-01-01', '2001-01-01'], 'WHERE `any-column` NOT BETWEEN \'2000-01-01\' AND \'2001-01-01\''],
            [[new Column('a'), new Column('b')], 'WHERE `any-column` NOT BETWEEN `a` AND `b`'],
            [[5, new Column('b')], 'WHERE `any-column` NOT BETWEEN 5 AND `b`'],
            [[new Column('a'), 5], 'WHERE `any-column` NOT BETWEEN `a` AND 5'],
        ];
    }

    /**
     * @dataProvider shouldCreateAOrBetweenStatementCorrectlyProvider
     */
    public function testShouldCreateAOrBetweenStatementCorrectly(array $values, string $expected)
    {
        $where = new Where();
        $where->orBetween('any-column', $values);

        $this->assertEquals($expected, $where);
    }

    public function shouldCreateAOrBetweenStatementCorrectlyProvider()
    {
        return [
            [[5, 10], 'WHERE `any-column` BETWEEN 5 AND 10'],
            [['2000-01-01', '2001-01-01'], 'WHERE `any-column` BETWEEN \'2000-01-01\' AND \'2001-01-01\''],
            [[new Column('a'), new Column('b')], 'WHERE `any-column` BETWEEN `a` AND `b`'],
            [[5, new Column('b')], 'WHERE `any-column` BETWEEN 5 AND `b`'],
            [[new Column('a'), 5], 'WHERE `any-column` BETWEEN `a` AND 5'],
        ];
    }

    /**
     * @dataProvider shouldCreateAOrNotBetweenStatementCorrectlyProvider
     */
    public function testShouldCreateAOrNotBetweenStatementCorrectly(array $values, string $expected)
    {
        $where = new Where();
        $where->orNotBetween('any-column', $values);

        $this->assertEquals($expected, $where);
    }

    public function shouldCreateAOrNotBetweenStatementCorrectlyProvider()
    {
        return [
            [[5, 10], 'WHERE `any-column` NOT BETWEEN 5 AND 10'],
            [['2000-01-01', '2001-01-01'], 'WHERE `any-column` NOT BETWEEN \'2000-01-01\' AND \'2001-01-01\''],
            [[new Column('a'), new Column('b')], 'WHERE `any-column` NOT BETWEEN `a` AND `b`'],
            [[5, new Column('b')], 'WHERE `any-column` NOT BETWEEN 5 AND `b`'],
            [[new Column('a'), 5], 'WHERE `any-column` NOT BETWEEN `a` AND 5'],
        ];
    }
}
