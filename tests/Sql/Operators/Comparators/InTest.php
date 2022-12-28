<?php

namespace Tests\Sql\Operators\Comparators;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Interfaces\ValueInterface;
use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Sql\Values\CollectionValue;
use QueryBuilder\Sql\Operators\Comparators\In;
use QueryBuilder\Exceptions\{
    InvalidArgumentColumnNameException,
    InvalidArgumentValuesException,
};

/**
 * @requires PHP 8.1
 */
class InTest extends TestCase
{
    private function makeSut(string $columnName, array $values): In
    {
        return new In($columnName, $values);
    }

    private function createRawValue(string $columnName): ValueInterface
    {
        return ValueFactory::createRawValue($columnName);
    }

    /**
     * @dataProvider shouldCreateAInOperatorCorrectlyProvider
     */
    public function testShouldCreateAInOperatorCorrectly(
        string $columnName,
        array $values,
        string $expected,
    ) {
        $sut = $this->makeSut($columnName, $values);

        $this->assertEquals($columnName, $sut->getColumn());
        $this->assertEquals(new CollectionValue($values), $sut->getValue());
        $this->assertEquals($expected, $sut);
    }

    public function shouldCreateAInOperatorCorrectlyProvider()
    {
        $now = $this->createRawValue('NOW()');

        return [
            ['any-column', [5, 10, 20.5], 'any-column IN (?, ?, ?)'],
            [
                'any-column',
                ['2000-01-01', '2001-01-01'],
                'any-column IN (?, ?)',
            ],
            ['any-column', [true], 'any-column IN (?)'],
            ['any-column', [$now], 'any-column IN (NOW())'],
        ];
    }

    /**
     * @dataProvider shouldCreateANotInOperatorCorrectlyProvider
     */
    public function testShouldCreateANotInOperatorCorrectly(
        string $columnName,
        array $values,
        string $expected,
    ) {
        $sut = $this->makeSut($columnName, $values);

        $this->assertEquals($expected, $sut->not());
    }

    public function shouldCreateANotInOperatorCorrectlyProvider()
    {
        $now = $this->createRawValue('NOW()');

        return [
            ['any-column', [5, 10, 20.5], 'any-column NOT IN (?, ?, ?)'],
            [
                'any-column',
                ['2000-01-01', '2001-01-01'],
                'any-column NOT IN (?, ?)',
            ],
            ['any-column', [true], 'any-column NOT IN (?)'],
            ['any-column', [$now], 'any-column NOT IN (NOW())'],
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

    public function testShouldThrowAnErrorIfTheSecondParameterOfTheConstructorIsAnEmptyArray()
    {
        $this->expectException(InvalidArgumentValuesException::class);
        $this->expectExceptionMessage(
            'The array of values ​​must not be empty.',
        );

        $this->makeSut('any-column', []);
    }
}
