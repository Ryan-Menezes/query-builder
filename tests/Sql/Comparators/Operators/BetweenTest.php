<?php

namespace Tests\Sql\Comparators\Operators;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Column;
use QueryBuilder\Sql\Values\RawValue;
use QueryBuilder\Sql\Comparators\Operators\Between;
use InvalidArgumentException;

/**
 * @requires PHP 8.1
 */
class BetweenTest extends TestCase
{
    /**
     * @dataProvider shouldCreateABetweenOperatorCorrectlyProvider
     */
    public function testShouldCreateABetweenOperatorCorrectly(string $column, array $values, string $expected)
    {
        $column = new Column($column);
        $between = new Between($column, $values);

        $this->assertEquals($column, $between->getColumn());
        // $this->assertEquals($values, $between->getValue());
        $this->assertEquals($expected, $between);
    }

    public function shouldCreateABetweenOperatorCorrectlyProvider()
    {
        return [
            ['any-column', [5, 10], '`any-column` BETWEEN ? AND ?'],
            ['any-column', ['2000-01-01', '2001-01-01'], '`any-column` BETWEEN ? AND ?'],
            ['any-column', [new Column('a'), new Column('b')], '`any-column` BETWEEN `a` AND `b`'],
            ['any-column', [5, new Column('b')], '`any-column` BETWEEN ? AND `b`'],
            ['any-column', [new Column('a'), 5], '`any-column` BETWEEN `a` AND ?'],
            ['any-column', ['2000-01-01', new RawValue('NOW()')], '`any-column` BETWEEN ? AND NOW()'],
        ];
    }

    /**
     * @dataProvider shouldCreateANotBetweenOperatorCorrectlyProvider
     */
    public function testShouldCreateANotBetweenOperatorCorrectly(string $column, array $values, string $expected)
    {
        $column = new Column($column);
        $between = new Between($column, $values);

        $this->assertEquals($expected, $between->not());
    }

    public function shouldCreateANotBetweenOperatorCorrectlyProvider()
    {
        return [
            ['any-column', [5, 10], '`any-column` NOT BETWEEN ? AND ?'],
            ['any-column', ['2000-01-01', '2001-01-01'], '`any-column` NOT BETWEEN ? AND ?'],
            ['any-column', [new Column('a'), new Column('b')], '`any-column` NOT BETWEEN `a` AND `b`'],
            ['any-column', [5, new Column('b')], '`any-column` NOT BETWEEN ? AND `b`'],
            ['any-column', [new Column('a'), 5], '`any-column` NOT BETWEEN `a` AND ?'],
            ['any-column', ['2000-01-01', new RawValue('NOW()')], '`any-column` NOT BETWEEN ? AND NOW()'],
        ];
    }

    /**
     * @dataProvider shouldReturnAnErrorIfWrongParametersArePassedToTheConstructorProvider
     */
    public function testShouldReturnAnErrorIfWrongParametersArePassedToTheConstructor(array $values)
    {
        $this->expectException(InvalidArgumentException::class);

        $column = new Column('any-column');
        new Between($column, $values);
    }

    public function shouldReturnAnErrorIfWrongParametersArePassedToTheConstructorProvider()
    {
        return [
            [[]],
            [[5]],
        ];
    }
}
