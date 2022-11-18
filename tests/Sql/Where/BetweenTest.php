<?php

namespace Tests\Sql\Where;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\Column;
use QueryBuilder\Sql\Where\Between;
use QueryBuilder\Sql\Values\RawValue;
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
        $between = new Between($column, $values);

        $actual = $between;

        $this->assertEquals($expected, $actual);
    }

    public function shouldCreateABetweenOperatorCorrectlyProvider()
    {
        return [
            ['any-column', [5, 10], '`any-column` BETWEEN 5 AND 10'],
            ['any-column', ['2000-01-01', '2001-01-01'], '`any-column` BETWEEN \'2000-01-01\' AND \'2001-01-01\''],
            ['any-column', [new Column('a'), new Column('b')], '`any-column` BETWEEN `a` AND `b`'],
            ['any-column', [5, new Column('b')], '`any-column` BETWEEN 5 AND `b`'],
            ['any-column', [new Column('a'), 5], '`any-column` BETWEEN `a` AND 5'],
            ['any-column', ['2000-01-01', new RawValue('NOW()')], '`any-column` BETWEEN \'2000-01-01\' AND NOW()'],
        ];
    }

    /**
     * @dataProvider shouldCreateANotBetweenOperatorCorrectlyProvider
     */
    public function testShouldCreateANotBetweenOperatorCorrectly(string $column, array $values, string $expected)
    {
        $between = new Between($column, $values);

        $actual = $between->not();

        $this->assertEquals($expected, $actual);
    }

    public function shouldCreateANotBetweenOperatorCorrectlyProvider()
    {
        return [
            ['any-column', [5, 10], '`any-column` NOT BETWEEN 5 AND 10'],
            ['any-column', ['2000-01-01', '2001-01-01'], '`any-column` NOT BETWEEN \'2000-01-01\' AND \'2001-01-01\''],
            ['any-column', [new Column('a'), new Column('b')], '`any-column` NOT BETWEEN `a` AND `b`'],
            ['any-column', [5, new Column('b')], '`any-column` NOT BETWEEN 5 AND `b`'],
            ['any-column', [new Column('a'), 5], '`any-column` NOT BETWEEN `a` AND 5'],
            ['any-column', ['2000-01-01', new RawValue('NOW()')], '`any-column` NOT BETWEEN \'2000-01-01\' AND NOW()'],
        ];
    }

    /**
     * @dataProvider shouldReturnAnErrorIfWrongParametersArePassedToTheConstructorProvider
     */
    public function testShouldReturnAnErrorIfWrongParametersArePassedToTheConstructor(array $values)
    {
        $this->expectException(InvalidArgumentException::class);

        new Between('any-column', $values);
    }

    public function shouldReturnAnErrorIfWrongParametersArePassedToTheConstructorProvider()
    {
        return [
            [[]],
            [[5]],
        ];
    }
}
