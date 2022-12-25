<?php

namespace Tests\Sql\Operators\Comparators;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Sql\Values\CollectionValue;
use QueryBuilder\Sql\Operators\Comparators\Between;
use QueryBuilder\Exceptions\{
    InvalidArgumentColumnNameException,
    InvalidArgumentValuesException,
};

/**
 * @requires PHP 8.1
 */
class BetweenTest extends TestCase
{
    /**
     * @dataProvider shouldCreateABetweenOperatorCorrectlyProvider
     */
    public function testShouldCreateABetweenOperatorCorrectly(
        string $columnName,
        array $values,
        string $expected,
    ) {
        $between = new Between($columnName, $values);

        $this->assertEquals($columnName, $between->getColumn());
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

    public function testShouldThrowAnErrorIfAnInvalidColumnNameIsPassed()
    {
        $this->expectException(InvalidArgumentColumnNameException::class);
        $this->expectExceptionMessage(
            'The column name must be a string of length greater than zero.',
        );

        $invalidColumnName = '';
        new Between($invalidColumnName, [5, 10]);
    }

    /**
     * @dataProvider shouldThrowAnErrorIfAnInvalidValuesIsPassedProvider
     */
    public function testShouldThrowAnErrorIfAnInvalidValuesIsPassed(
        array $values,
    ) {
        $this->expectException(InvalidArgumentValuesException::class);
        $this->expectExceptionMessage(
            'The array of values ​​must contain only two values.',
        );

        new Between('any-column', $values);
    }

    public function shouldThrowAnErrorIfAnInvalidValuesIsPassedProvider()
    {
        return [[[]], [[5]]];
    }
}
