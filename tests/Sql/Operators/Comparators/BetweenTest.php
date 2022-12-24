<?php

namespace Tests\Sql\Operators\Comparators;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Sql\Operators\Comparators\Between;
use QueryBuilder\Sql\Values\CollectionValue;
use InvalidArgumentException;

/**
 * @requires PHP 8.1
 */
class BetweenTest extends TestCase
{
    /**
     * @dataProvider shouldCreateABetweenOperatorCorrectlyProvider
     */
    public function testShouldCreateABetweenOperatorCorrectly(
        string $column,
        array $values,
        string $expected,
    ) {
        $between = new Between($column, $values);

        $this->assertEquals($column, $between->getColumn());
        $this->assertEquals(new CollectionValue($values), $between->getValue());
        $this->assertEquals($expected, $between);
    }

    public function shouldCreateABetweenOperatorCorrectlyProvider()
    {
        $columnA = ValueFactory::createRawValue('a');
        $columnB = ValueFactory::createRawValue('b');
        $now = ValueFactory::createRawValue('NOW()');

        return [
            ['any-column', [5, 10], 'any-column BETWEEN ? AND ?'],
            [
                'any-column',
                ['2000-01-01', '2001-01-01'],
                'any-column BETWEEN ? AND ?',
            ],
            ['any-column', [$columnA, $columnB], 'any-column BETWEEN a AND b'],
            ['any-column', [5, $columnB], 'any-column BETWEEN ? AND b'],
            ['any-column', [$columnA, 5], 'any-column BETWEEN a AND ?'],
            [
                'any-column',
                ['2000-01-01', $now],
                'any-column BETWEEN ? AND NOW()',
            ],
        ];
    }

    /**
     * @dataProvider shouldCreateANotBetweenOperatorCorrectlyProvider
     */
    public function testShouldCreateANotBetweenOperatorCorrectly(
        string $column,
        array $values,
        string $expected,
    ) {
        $between = new Between($column, $values);

        $this->assertEquals($expected, $between->not());
    }

    public function shouldCreateANotBetweenOperatorCorrectlyProvider()
    {
        $columnA = ValueFactory::createRawValue('a');
        $columnB = ValueFactory::createRawValue('b');
        $now = ValueFactory::createRawValue('NOW()');

        return [
            ['any-column', [5, 10], 'any-column NOT BETWEEN ? AND ?'],
            [
                'any-column',
                ['2000-01-01', '2001-01-01'],
                'any-column NOT BETWEEN ? AND ?',
            ],
            [
                'any-column',
                [$columnA, $columnB],
                'any-column NOT BETWEEN a AND b',
            ],
            ['any-column', [5, $columnB], 'any-column NOT BETWEEN ? AND b'],
            ['any-column', [$columnA, 5], 'any-column NOT BETWEEN a AND ?'],
            [
                'any-column',
                ['2000-01-01', $now],
                'any-column NOT BETWEEN ? AND NOW()',
            ],
        ];
    }

    /**
     * @dataProvider shouldReturnAnErrorIfWrongParametersArePassedToTheConstructorProvider
     */
    public function testShouldReturnAnErrorIfWrongParametersArePassedToTheConstructor(
        array $values,
    ) {
        $this->expectException(InvalidArgumentException::class);

        new Between('any-column', $values);
    }

    public function shouldReturnAnErrorIfWrongParametersArePassedToTheConstructorProvider()
    {
        return [[[]], [[5]]];
    }
}