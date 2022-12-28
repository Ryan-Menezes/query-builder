<?php

namespace Tests\Sql\Operators\Comparators;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Interfaces\ValueInterface;
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
    private function makeSut(string $columnName, array $values): Between
    {
        return new Between($columnName, $values);
    }

    private function createRawValue(string $columnName): ValueInterface
    {
        return ValueFactory::createRawValue($columnName);
    }

    /**
     * @dataProvider shouldCreateABetweenOperatorCorrectlyProvider
     */
    public function testShouldCreateABetweenOperatorCorrectly(
        string $columnName,
        array $values,
        string $expected,
    ) {
        $sut = $this->makeSut($columnName, $values);

        $this->assertEquals($columnName, $sut->getColumn());
        $this->assertEquals(new CollectionValue($values), $sut->getValue());
        $this->assertEquals($expected, $sut);
    }

    public function shouldCreateABetweenOperatorCorrectlyProvider()
    {
        $columnA = $this->createRawValue('a');
        $columnB = $this->createRawValue('b');
        $now = $this->createRawValue('NOW()');

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
        string $columnName,
        array $values,
        string $expected,
    ) {
        $sut = $this->makeSut($columnName, $values);

        $this->assertEquals($expected, $sut->not());
    }

    public function shouldCreateANotBetweenOperatorCorrectlyProvider()
    {
        $columnA = $this->createRawValue('a');
        $columnB = $this->createRawValue('b');
        $now = $this->createRawValue('NOW()');

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
        $this->makeSut($invalidColumnName, [5, 10]);
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

        $this->makeSut('any-column', $values);
    }

    public function shouldThrowAnErrorIfAnInvalidValuesIsPassedProvider()
    {
        return [[[]], [[5]]];
    }
}
