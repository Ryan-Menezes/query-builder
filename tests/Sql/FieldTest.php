<?php

namespace Tests\Sql;

use PHPUnit\Framework\TestCase;

use QueryBuilder\Factories\ValueFactory;
use QueryBuilder\Sql\Values\RawValue;
use QueryBuilder\Sql\{
    Field,
    Column,
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
        $column = new Column('any-column');
        $value = ValueFactory::createValue($value);
        $field = new Field($column, '=', $value);

        $this->assertEquals($column, $field->getColumn());
        $this->assertEquals('=', $field->getOperator());
        $this->assertEquals($value, $field->getValue());
        $this->assertEquals('`any-column` = ?', $field);
    }

    public function shouldReturnAFormattedStringAndItsRespectiveAssignmentOrComparisonValueProvider()
    {
        return [
            ['any-string'],
            [5],
            [12.5],
            [true],
            [null],
        ];
    }

    public function testShouldAcceptARawValue()
    {
        $column = new Column('any-column');
        $value = new RawValue('COUNT(*)');
        $field = new Field($column, '=', $value);

        $this->assertEquals($column, $field->getColumn());
        $this->assertEquals('=', $field->getOperator());
        $this->assertEquals($value, $field->getValue());
        $this->assertEquals('`any-column` = COUNT(*)', $field);
    }

    /**
     * @dataProvider shouldCorrectlyAcceptAndFormatTheColumnPassedInTheFirstParameterProvider
     */
    public function testShouldCorrectlyAcceptAndFormatTheColumnPassedInTheFirstParameter(string $column, string $expected)
    {
        $column = new Column($column);
        $value = ValueFactory::createValue('any-value');
        $field = new Field($column, '=', $value);

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
    public function testShouldAcceptAColumnAsSecondParameter(string $value, string $expected)
    {
        $column = new Column('any-column');
        $value = new Column($value);
        $field = new Field($column, '=', $value);

        $this->assertEquals($column, $field->getColumn());
        $this->assertEquals('=', $field->getOperator());
        $this->assertEquals($value, $field->getValue());
        $this->assertEquals($expected, $field);
    }

    public function shouldAcceptAColumnAsSecondParameterProvider()
    {
        return [
            ['other-column', '`any-column` = `other-column`'],
            ['`other-column`', '`any-column` = `other-column`'],
            ['`any-table`.`other-column`', '`any-column` = `any-table`.`other-column`'],
            ['any-table.other-column', '`any-column` = `any-table`.`other-column`'],
        ];
    }
}
