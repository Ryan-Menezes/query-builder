<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Sql\Field;
use QueryBuilder\Exceptions\{
    InvalidArgumentColumnNameException,
    InvalidArgumentOperatorException,
};
use QueryBuilder\Sql\Values\RawValue;

/**
 * @requires PHP 8.1
 */
class FieldTest extends TestCase
{
    /**
     * @dataProvider shouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValueProvider
     */
    public function testShouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValue(
        mixed $value,
        string $expected,
    ) {
        $columnName = 'any-column';
        $value = ValueFactory::createValue($value);
        $field = new Field($columnName, '=', $value);

        $this->assertEquals($columnName, $field->getColumn());
        $this->assertEquals('=', $field->getOperator());
        $this->assertEquals($value, $field->getValue());
        $this->assertEquals($expected, $field);
    }

    public function shouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValueProvider()
    {
        return [
            ['any-string', 'any-column = ?'],
            [5, 'any-column = ?'],
            [12.5, 'any-column = ?'],
            [true, 'any-column = ?'],
            [null, 'any-column = ?'],
            [[1, 2, 3], 'any-column = (?, ?, ?)'],
            [new RawValue('COUNT(*)'), 'any-column = COUNT(*)'],
        ];
    }

    public function testShouldThrowAnErrorIfAnInvalidColumnNameIsPassed()
    {
        $this->expectException(InvalidArgumentColumnNameException::class);
        $this->expectExceptionMessage(
            'The column name must be a string of length greater than zero.',
        );

        $invalidColumnName = '';
        $value = ValueFactory::createRawValue('any-value');

        new Field($invalidColumnName, '=', $value);
    }

    public function testShouldThrowAnErrorIfAnInvalidOperatorIsPassed()
    {
        $this->expectException(InvalidArgumentOperatorException::class);
        $this->expectExceptionMessage(
            'The operator must be a string of length greater than zero.',
        );

        $columnName = 'any-column';
        $invalidOperator = '';
        $value = ValueFactory::createRawValue('any-value');

        new Field($columnName, $invalidOperator, $value);
    }
}
