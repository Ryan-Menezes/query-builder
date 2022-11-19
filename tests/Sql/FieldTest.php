<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Sql\{
    Field,
    Column,
};
use QueryBuilder\Sql\Values\{
    StringValue,
    NumberValue,
    BooleanValue,
    NullValue,
    RawValue,
};

/**
 * @requires PHP 8.1
 */
class FieldTest extends TestCase
{
    /**
     * @dataProvider shouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValueProvider
     */
    public function testShouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValue(mixed $value)
    {
        $field = new Field('any-column', '=', $value);

        $this->assertEquals($value, $field->getValue());
        $this->assertEquals('`any-column` = ?', $field);
    }

    public function shouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValueProvider()
    {
        return [
            [new StringValue('any-string')],
            [new NumberValue(5)],
            [new NumberValue(12.5)],
            [new BooleanValue(true)],
            [new NullValue()],
        ];
    }

    public function testShouldAcceptARawValue()
    {
        $value = new RawValue('COUNT(*)');
        $field = new Field('any-column', '=', $value);

        $this->assertEquals($value, $field->getValue());
        $this->assertEquals('`any-column` = COUNT(*)', $field);
    }

    /**
     * @dataProvider shouldCorrectlyAcceptAndFormatTheColumnPassedInTheFirstParameterProvider
     */
    public function testShouldCorrectlyAcceptAndFormatTheColumnPassedInTheFirstParameter(string $column, string $expected)
    {
        $field = new Field($column, '=', 'any-value');

        $this->assertEquals($expected, $field);
    }

    public function shouldCorrectlyAcceptAndFormatTheColumnPassedInTheFirstParameterProvider()
    {
        return [
            ['`any-column`', '`any-column` = ?'],
            ['any-column', '`any-column` = ?'],
            ['`any-table`.`any-column`', '`any-table`.`any-column` = ?'],
            ['any-table.any-column', '`any-table`.`any-column` = ?'],
        ];
    }

    /**
     * @dataProvider shouldAcceptAColumnAsSecondParameterProvider
     */
    public function testShouldAcceptAColumnAsSecondParameter(Column $column, string $expected)
    {
        $field = new Field('any-column', '=', $column);

        $this->assertEquals($column, $field->getValue());
        $this->assertEquals($expected, $field);
    }

    public function shouldAcceptAColumnAsSecondParameterProvider()
    {
        return [
            [new Column('other-column'), '`any-column` = `other-column`'],
            [new Column('`other-column`'), '`any-column` = `other-column`'],
            [new Column('`any-table`.`other-column`'), '`any-column` = `any-table`.`other-column`'],
            [new Column('any-table.other-column'), '`any-column` = `any-table`.`other-column`'],
        ];
    }
}
