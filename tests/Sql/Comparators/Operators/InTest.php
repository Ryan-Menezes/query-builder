<?php

namespace Tests\Sql\Comparators\Operators;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Column;
use QueryBuilder\Sql\Values\RawValue;
use QueryBuilder\Sql\Comparators\Operators\In;
use InvalidArgumentException;

/**
 * @requires PHP 8.1
 */
class InTest extends TestCase
{
    /**
     * @dataProvider shouldCreateAInOperatorCorrectlyProvider
     */
    public function testShouldCreateAInOperatorCorrectly(string $column, array $values, string $expected)
    {
        $column = new Column($column);
        $in = new In($column, $values);

        $this->assertEquals($expected, $in);
    }

    public function shouldCreateAInOperatorCorrectlyProvider()
    {
        return [
            ['any-column', [5, 10, 20.5], '`any-column` IN (5, 10, 20.5)'],
            ['any-column', ['2000-01-01', '2001-01-01'], '`any-column` IN (\'2000-01-01\', \'2001-01-01\')'],
            ['any-column', [true], '`any-column` IN (1)'],
            ['any-column', [new RawValue('NOW()')], '`any-column` IN (NOW())'],
        ];
    }

    /**
     * @dataProvider shouldCreateANotInOperatorCorrectlyProvider
     */
    public function testShouldCreateANotInOperatorCorrectly(string $column, array $values, string $expected)
    {
        $column = new Column($column);
        $in = new In($column, $values);

        $this->assertEquals($expected, $in->not());
    }

    public function shouldCreateANotInOperatorCorrectlyProvider()
    {
        return [
            ['any-column', [5, 10, 20.5], '`any-column` NOT IN (5, 10, 20.5)'],
            ['any-column', ['2000-01-01', '2001-01-01'], '`any-column` NOT IN (\'2000-01-01\', \'2001-01-01\')'],
            ['any-column', [true], '`any-column` NOT IN (1)'],
            ['any-column', [new RawValue('NOW()')], '`any-column` NOT IN (NOW())'],
        ];
    }
    public function testShouldReturnAnErrorIfTheSecondParameterOfTheConstructorIsAnEmptyArray()
    {
        $this->expectException(InvalidArgumentException::class);

        $column = new Column('any-column');
        new In($column, []);
    }
}
