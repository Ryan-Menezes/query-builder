<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Interfaces\ValueInterface;
use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Sql\Field;
use QueryBuilder\Exceptions\{
    InvalidArgumentColumnNameException,
    InvalidArgumentOperatorException,
};

/**
 * @requires PHP 8.1
 */
class FieldTest extends TestCase
{
    private function makeSut(
        string $columnName,
        string $operator,
        mixed $value,
    ): array {
        $value = ValueFactory::createValue($value);
        $field = new Field($columnName, $operator, $value);

        return [$field, $value];
    }

    private function createRawValue(string $columnName): ValueInterface
    {
        return ValueFactory::createRawValue($columnName);
    }

    /**
     * @dataProvider shouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValueProvider
     */
    public function testShouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValue(
        mixed $value,
        string $expected,
    ) {
        $columnName = 'any-column';
        $operator = '=';
        [$sut, $value] = $this->makeSut($columnName, $operator, $value);
        $expectedColumn = $this->createRawValue($columnName);

        $this->assertEquals($expectedColumn, $sut->getColumn());
        $this->assertEquals($operator, $sut->getOperator());
        $this->assertEquals($value, $sut->getValue());
        $this->assertEquals($expected, $sut);
    }

    public function shouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValueProvider()
    {
        $count = $this->createRawValue('COUNT(*)');

        return [
            ['any-string', 'any-column = ?'],
            [5, 'any-column = ?'],
            [12.5, 'any-column = ?'],
            [true, 'any-column = ?'],
            [null, 'any-column = ?'],
            [[1, 2, 3], 'any-column = (?, ?, ?)'],
            [$count, 'any-column = COUNT(*)'],
        ];
    }

    public function testShouldThrowAnErrorIfAnInvalidColumnNameIsPassed()
    {
        $this->expectException(InvalidArgumentColumnNameException::class);
        $this->expectExceptionMessage(
            'The column name must be a string of length greater than zero.',
        );

        $invalidColumnName = '';
        $operator = '=';
        $value = 'any-value';

        $this->makeSut($invalidColumnName, $operator, $value);
    }

    public function testShouldThrowAnErrorIfAnInvalidOperatorIsPassed()
    {
        $this->expectException(InvalidArgumentOperatorException::class);
        $this->expectExceptionMessage(
            'The operator must be a string of length greater than zero.',
        );

        $columnName = 'any-column';
        $invalidOperator = '';
        $value = 'any-value';

        $this->makeSut($columnName, $invalidOperator, $value);
    }
}
