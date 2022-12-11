<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Sql\Field;

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
        $column = 'any-column';
        $value = ValueFactory::createValue($value);
        $field = new Field($column, '=', $value);

        $this->assertEquals($column, $field->getColumn());
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
        ];
    }

    public function testShouldAcceptARawValue()
    {
        $column = 'any-column';
        $value = ValueFactory::createRawValue('COUNT(*)');
        $field = new Field($column, '=', $value);

        $this->assertEquals($column, $field->getColumn());
        $this->assertEquals('=', $field->getOperator());
        $this->assertEquals($value, $field->getValue());
        $this->assertEquals('any-column = COUNT(*)', $field);
    }
}
